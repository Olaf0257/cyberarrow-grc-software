<?php

namespace App\Http\Controllers\ThirdPartyRisk;

use App\Exports\QuestionsTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\QuestionsImport;
use App\Models\ThirdPartyRisk\Domain;
use App\Models\ThirdPartyRisk\Question;
use App\Models\ThirdPartyRisk\Questionnaire;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class QuestionController extends Controller
{
    const QUESTIONS_HOME = 'third-party-risk.questionnaires.questions.index';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Questionnaire $questionnaire)
    {
        return Inertia::render('third-party-risk/questions/Index', compact('questionnaire'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Questionnaire $questionnaire)
    {
        abort_if($questionnaire->is_default, 403);
        $domains = Domain::all();

        return Inertia::render('third-party-risk/questions/Create', compact(
            'domains',
            'questionnaire'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Questionnaire $questionnaire, Request $request)
    {
        abort_if($questionnaire->is_default, 403);
        $request->validate([
            'text' => [
                'bail',
                'required',
                'max:825',
                'string',
                Rule::unique('third_party_questions')->where(function ($query) use ($questionnaire) {
                    return $query->where('questionnaire_id', $questionnaire->id);
                })
            ],
            'domain_id' => 'bail|required|exists:third_party_domains,id'
        ], [
            'domain_id.required' => 'The domain field is required.',
            'text.unique' => 'This question already exists.'
        ]);

        $questionnaire->questions()->create($request->only(['text', 'domain_id']));
        Log::info('New question was added to questionnaire', ['questionnaire' => $questionnaire->id]);

        return redirect()->to(route(self::QUESTIONS_HOME, [$questionnaire->id]))->withSuccess('Question added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\ThirdPartyRisk\Question $question
     * @return \Illuminate\Http\Response
     */
    public function show(Question $question)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\ThirdPartyRisk\Question $question
     * @return \Inertia\Response|RedirectResponse
     */
    public function edit(Questionnaire $questionnaire, Question $question)
    {
        abort_if($questionnaire->is_default, 403);

        $domains = Domain::all();
        return Inertia::render('third-party-risk/questions/Edit', compact(
            'question',
            'questionnaire',
            'domains'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ThirdPartyRisk\Question $question
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Questionnaire $questionnaire, Question $question)
    {
        abort_if($questionnaire->is_default, 403);
        $request->validate([
            'text' => [
                'bail',
                'required',
                'max:825',
                'string',
                Rule::unique('third_party_questions')->where(function ($query) use ($questionnaire) {
                    return $query->where('questionnaire_id', $questionnaire->id);
                })->ignore($question->id)
            ],
            'domain_id' => 'bail|required|exists:third_party_domains,id'
        ], [
            'domain_id.required' => 'The domain field is required.',
            'text.unique' => 'This question already exists.'
        ]);

        $question->update($request->only(['text', 'domain_id']));

        Log::info('Question was updated', ['question' => $question->id]);

        return redirect()->to(route(self::QUESTIONS_HOME, [$questionnaire->id]))->withSuccess('Question updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ThirdPartyRisk\Question $question
     * @return \Illuminate\Http\Response
     */
    public function destroy(Questionnaire $questionnaire, Question $question)
    {
        abort_if($questionnaire->is_default, 403);
        $question_id = $question->id;
        $question->delete();

        Log::info('Question was deleted', ['question' => $question_id]);

        return redirect()->back()->withSuccess('Question deleted successfully.');
    }

    public function getJsonData(Questionnaire $questionnaire, Request $request)
    {
        $builder = Question::query()
            ->where('questionnaire_id', $questionnaire->id)
            ->with('domain');
        $per_page = 10;

        if ($request->has('search')) {
            $builder->where('text', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->has('per_page')) {
            $per_page = $request->per_page;
        }

        $data = $builder->orderByDesc('id')->paginate($per_page);

        return response()->json(['data' => $data]);
    }

    public function downloadSample()
    {
        return Excel::download(new QuestionsTemplateExport, '3rd-party-risks-sample.csv');
    }

    public function batchImport(Questionnaire $questionnaire, Request $request)
    {

        $request->validate([
            'csv_file' => 'bail|required|mimes:csv,txt'
        ]);

        try {
            Excel::import(new QuestionsImport($questionnaire->id), $request->file('csv_file'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }

        Log::info('Questions were added by csv upload', ['questionnaire' => $questionnaire->id]);

        return redirect()->to(route(self::QUESTIONS_HOME, $questionnaire->id))->withSuccess('Questions imported successfully');
    }
}
