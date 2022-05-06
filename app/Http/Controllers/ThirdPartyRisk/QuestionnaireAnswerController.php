<?php

namespace App\Http\Controllers\ThirdPartyRisk;

use App\Http\Controllers\Controller;
use App\Models\ThirdPartyRisk\ProjectActivity;
use App\Models\ThirdPartyRisk\ProjectEmail;
use App\Models\ThirdPartyRisk\QuestionAnswer;
use App\Models\ThirdPartyRisk\Questionnaire;
use App\Models\ThirdPartyRisk\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class QuestionnaireAnswerController extends Controller
{
    public function show($token, Request $request)
    {
        $user_project_email = ProjectEmail::with('project.vendor')
            ->with('project.questionnaire.questions', function ($query) {
                $query->select('id', 'text', 'questionnaire_id');
            })
            ->where('token', $token)->firstOrFail();

        $project = $user_project_email->project;
        $questionnaire = Questionnaire::with(['questions.single_answer' => function ($q) use ($project) {
            $q->where('project_id', $project->id)->latest();
        }])->find($project->questionnaire_id);

        $questions = $questionnaire->questions;
        $vendor = $project->vendor;

        ProjectActivity::create([
            'project_id' => $project->id,
            'activity' => 'Acknowledgment link clicked',
            'type' => 'clicked-link',
        ]);
        Log::info('Vendor accessed questionnaire', ['project_id' => $project->id, 'vendor_id' => $project->vendor_id]);

        $can_respond = $project->email->status === 'pending';
        return Inertia::render('third-party-risk/questionnaires/Show', compact('questions', 'project', 'vendor', 'can_respond', 'token'));
    }

    public function store(Request $request)
    {
        $answer_options = ['Yes', 'No', 'Partial', 'Not Applicable'];
        $request->validate([
            'token' => 'required|exists:third_party_project_emails',
            'answers.*.answer' => 'required|in:' . implode(',', $answer_options),
            'answers.*.question_id' => 'required|exists:third_party_questions,id',
        ]);

        $user_project_email = ProjectEmail::with('project.questionnaire')->where('token', $request->token)->first();

        $request_answers = $request->answers;

        $project = $user_project_email->project;

        abort_if($project->email->status === 'completed', 403);
        $answers = array_map(function ($answer) use ($project) {
            $current_timestamp = Carbon::now()->format('Y-m-d H:i:s');

            return [
                'question_id' => $answer["question_id"],
                'answer' => $answer["answer"],
                'project_id' => $project->id,
                'created_at' => $current_timestamp,
                'updated_at' => $current_timestamp,
            ];
        }, $request_answers);

        try {

            QuestionAnswer::insert($answers);

            ProjectActivity::create([
                'project_id' => $project->id,
                'activity' => "Answered questionnaire",
                'type' => "questionnaire-answers"
            ]);

            //get the score for current project
            $answers_score = 0;
            $answers_count = 0;
            foreach($answers as $answer) {
                switch ($answer["answer"]) {
                    case "Yes":
                        $answers_score += 1;
                        $answers_count++;
                        break;
                    case "Partial":
                        $answers_score += 0.5;
                        $answers_count++;
                        break;
                    case "No":
                        $answers_count++;
                    default:
                        break;
                }
            }

            // mitigate division by 0. That should not be allowed
            $answers_count = $answers_count === 0 ? 1 : $answers_count;
            $project_score = ($answers_score / $answers_count) * 100;

            $project->status = "archived";
            $project->score = $project_score;
            $project->save();

            // get the score for vendor. Take into account previous projects for the vendor. Score is the average between vendor projects
            $vendor = Vendor::with(['projects' => function ($q) {
                $q->where('score', '!=', null);
            }])->find($project->vendor_id);

            $vendor_projects = $vendor->projects;
            $total_vendor_projects_score = 0;
            $projects_cont = $vendor_projects->count();
            foreach ($vendor_projects as $vendor_project) {
                $vendor_project_score = $vendor_project->score;
                if($vendor_project_score) {
                    $total_vendor_projects_score += $vendor_project_score;
                }
            }

            $vendor_score = $total_vendor_projects_score / $projects_cont;
            $vendor->score = $vendor_score;
            $vendor->save();

            $user_project_email->status = "completed";
            $user_project_email->save();

            Log::info('Vendor saved questionnaire answers', ['project_id' => $project->id, 'vendor_id' => $project->vendor_id]);

        } catch (\Exception $exception) {

            Log::info('Vendor could not save questionnaire answers', ['project_id' => $project->id, 'vendor_id' => $project->vendor_id]);
            return redirect()->back()->with([
                'error' => 'Could not save questions',
            ]);

        }

        return "success";
    }
}
