<?php

namespace App\Http\Controllers\ThirdPartyRisk;

use App\Exports\ProjectDetailsExport;
use App\Http\Controllers\Controller;
use App\Mail\ThirdPartyRisk\ProjectReminder;
use App\Models\GlobalSettings\GlobalSetting;
use App\Models\ThirdPartyRisk\Project;
use App\Models\ThirdPartyRisk\ProjectActivity;
use App\Models\ThirdPartyRisk\ProjectEmail;
use App\Models\ThirdPartyRisk\Question;
use App\Models\ThirdPartyRisk\QuestionAnswer;
use App\Models\ThirdPartyRisk\Questionnaire;
use App\Mail\ThirdPartyRisk\Questionnaire as QuestionnaireMail;
use App\Models\ThirdPartyRisk\Vendor;
use App\Rules\common\UniqueWithinDataScope;
use App\Traits\Timezone;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ProjectController extends Controller
{
    use Timezone;

    private $appTimezone;

    const PROJECT_FREQUENCIES = ['Weekly', 'Biweekly', 'Monthly', 'Bi-anually', 'Annually'];

    public function __construct()
    {
        $this->middleware('data_scope')->except('index', 'show', 'sendReminder', 'answers');
        $this->appTimezone = GlobalSetting::query()->first('timezone')->timezone;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $timezones = [];
        foreach ($this->appTimezone() as $value => $label) {
            $timezones[] = [
                'label' => $label,
                'value' => $value
            ];
        }
        $frequencies = collect(self::PROJECT_FREQUENCIES)->map(function ($frequency) {
            return [
                'label' => $frequency,
                'value' => $frequency
            ];
        });

        return Inertia::render('third-party-risk/projects/Index', compact('timezones', 'frequencies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*
         * 'launch_date' => 'bail|required|date|after:today',
         * 'due_date' => 'bail|required|date|after:launch_date'
         */
        $request->validate([
            'name' => [
                'bail',
                'required',
                'max:191',
                new UniqueWithinDataScope(new Project, 'name')
            ],
            'questionnaire_id' => 'bail|required|exists:third_party_questionnaires,id',
            'vendor_id' => 'bail|required|exists:third_party_vendors,id',
            'frequency' => [
                'bail',
                'required',
                Rule::in(self::PROJECT_FREQUENCIES)
            ],
            'timezone' => [
                'bail',
                'required',
                Rule::in(array_keys($this->appTimezone()))
            ],
            'launch_date' => 'bail|required|date|after:today',
            'due_date' => 'bail|required|date|after:launch_date'
        ], [
            'questionnaire_id.required' => 'The questionnaire field is required.',
            'vendor_id.required' => 'The vendor field is required.'
        ]);

        $project = Project::create($request->only([
            'name',
            'questionnaire_id',
            'launch_date',
            'due_date',
            'timezone',
            'frequency',
            'vendor_id'
        ]));

        ProjectEmail::create([
            'project_id' => $project->id,
            'token' => encrypt($project->id . '-' . $project->vendor_id . date('r', time())),
        ]);

        // executing shell script without waiting for it
        if (env('PHP_CONFLICT') && env('PHP8_BINARY')) {
            $cmd = 'cd ' . base_path() . ' && ' . env('PHP8_BINARY') . ' artisan tenants:run schedule:run ' . "> /dev/null 2>/dev/null &";
        } else {
            $cmd = 'cd ' . base_path() . ' && php artisan tenants:run schedule:run ' . "> /dev/null 2>/dev/null &";
        }
        shell_exec($cmd);


        return redirect()->back()->withSuccess('Project added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $project = Project::with(['questionnaire', 'vendor'])->findOrFail($id);

        $project['launch_date'] = Carbon::createFromFormat('Y-m-d H:i:s', $project->launch_date, 'UTC')->setTimezone($this->appTimezone);
        $project['due_date'] = Carbon::createFromFormat('Y-m-d H:i:s', $project->due_date, 'UTC')->setTimezone($this->appTimezone);

        return Inertia::render('third-party-risk/projects/Show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Project $project)
    {
        $project_id = $project->id;
        $success = $project->delete();

        if ($success) {
            Log::info('User has deleted a project.', ['project_id' => $project_id]);
            return redirect()->back()->withSuccess("Project deleted successfully.");
        }

        Log::info('User tried to delete a project but it was not deleted', ['project_id' => $project_id]);
        return redirect()->back()->withErrors("Could not delete project.");
    }

    public function options(Request $request)
    {
        $questionnaires = Questionnaire::whereHas('questions')->get()->map(function ($questionnaire) {
            return [
                'label' => $questionnaire->name,
                'value' => $questionnaire->id,
            ];
        });

        $vendors = Vendor::all()->map(function ($vendor) {
            return [
                'label' => $vendor->name,
                'value' => $vendor->id
            ];
        });

        return response()->json(compact('vendors', 'questionnaires'));
    }

    public function getJsonData(Request $request)
    {
        $request->validate([
            'search' => 'string|nullable',
            'filter' => 'in:archived,active|nullable'
        ]);

        $projects = Project::query()
            ->with('vendor')
            ->orderByDesc('id')
            ->when($request->input('search'), function ($query) use ($request) {
                return $query->where('name', 'LIKE', '%' . $request->search . '%');
            })
            ->when($request->input('filter'), function ($query) use ($request) {
                return $query->where('status', $request->filter);
            })
            ->get()
            ->each(function ($project) {
                $project['launch_date'] = Carbon::createFromFormat('Y-m-d H:i:s', $project->launch_date, 'UTC')->setTimezone($this->appTimezone);
                $project['due_date'] = Carbon::createFromFormat('Y-m-d H:i:s', $project->due_date, 'UTC')->setTimezone($this->appTimezone);
            });
        return response()->json([
            'projects' => $projects
        ]);
    }

    public function sendReminder(Request $request, $id)
    {
        $project = Project::with('vendor', 'email', 'questionnaire')->find($id);
        $vendor = $project->vendor;
        $email_token = $project->email;

        $error_messages = [];
        if (in_array($project->project_status['status'], ["Not Started", "Completed"])) {
            $error_messages[] = "Can't send reminder to a project which has not been started yet or that is completed.";
        }

        if (!$email_token) {
            ProjectActivity::create([
                'project_id' => $project->id,
                'activity' => 'Error Sending Reminder Email',
                'type' => 'reminder-email-error',
            ]);

            Log::info('User has made a third party risk email reminder. The request failed, the project does not have a vendor email attached', ['project_id' => $id]);
            $error_messages[] = "Failed to send email. Project does not have a vendor email attached to it.";

        }

        if (!empty($error_messages)) {
            return redirect()->back()->withErrors($error_messages);
        }

        try {
            Mail::to($vendor->email)->send(new ProjectReminder($email_token, $project, $vendor));

            ProjectActivity::create([
                'project_id' => $project->id,
                'activity' => 'Email Reminder Sent on request',
                'type' => 'reminder-email-sent',
            ]);
        } catch (\Exception $e) {
            ProjectActivity::create([
                'project_id' => $project->id,
                'activity' => 'Error Sending Reminder Email',
                'type' => 'reminder-email-error',
            ]);

            Log::info('User has made a third party risk email reminder. The request failed', ['project_id' => $id]);
            return redirect()->back()->with(['exception' => 'Failed to process request. Please check SMTP authentication connection.']);
        }


        Log::info('User has made a third party risk email reminder', ['project_id' => $id]);
        return redirect()->back()->with('success', 'Third party project reminder email sent to vendor.');
    }

    public function answers(Project $project, Request $request)
    {
        $data = $project->questionnaire->questions()
            ->when($request->input('search'), function ($query) use ($request) {
                return $query->where('text', 'LIKE', '%' . $request->search . '%');
            })
            ->with(['single_answer' => function ($q) use ($project) {
                $q->where('project_id', $project->id)->latest();
            }])->paginate($request->per_page);

        return response()->json([
            'data' => $data
        ]);
    }

    public function exportCSV(Project $project)
    {
        return Excel::download(new ProjectDetailsExport($project), 'project-details.csv');
    }

    public function exportPDF(Request $request, $id)
    {
        $project = Project::with('questionnaire.questions.single_answer')
            ->with('vendor', function ($query) {
                $query->select('id', 'name');
            })
            ->with('questionnaire.questions.single_answer', function ($q) use ($id) {
                $q->where('project_id', $id)->latest();
            })
            ->find($id);

        $score = $project->score;
        if ($score < 21) {
            $level = 1;
            $color = "#ff0000";
        } else if ($score >= 21 && $score < 41) {
            $level = 2;
            $color = "#ffc000";
        } else if ($score >= 41 && $score < 61) {
            $level = 3;
            $color = "#ffff00";
        } else if ($score >= 61 && $score < 81) {
            $level = 4;
            $color = "#92d050";
        } else {
            $level = 5;
            $color = "#00b050";
        }

        $project->setAttribute("color", $color);
        $project->setAttribute("level", $level);
        $data = ['project' => $project, 'timezone' => $this->appTimezone()[$project->timezone]];

        $pdf = \PDF::loadView('third-party-risks.project-pdf-report', $data);
        $pdf->setOptions([
            'enable-local-file-access' => true,
            'enable-javascript' => true,
            'javascript-delay' => 3000,
            'enable-smart-shrinking' => true,
            'no-stop-slow-scripts' => true,
            'header-center' => 'Note: This is a system generated report',
            'footer-center' => 'Third Party Risk Report',
            'footer-left' => 'Confidential',
            'footer-right' => '[page]',
            'debug-javascript' => true,
        ]);

        Log::info('User has downloaded a third party risk project report as pdf.', ['project' => $id]);
        return $pdf->inline('third-party-project-details.pdf');
    }
}
