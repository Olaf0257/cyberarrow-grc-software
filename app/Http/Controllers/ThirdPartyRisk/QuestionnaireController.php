<?php

namespace App\Http\Controllers\ThirdPartyRisk;

use App\Http\Controllers\Controller;
use App\Models\ThirdPartyRisk\Domain;
use App\Models\ThirdPartyRisk\Question;
use App\Models\ThirdPartyRisk\Questionnaire;
use App\Rules\UniqueQuestionnaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class QuestionnaireController extends Controller
{

    public function __construct() {
        $this->middleware('data_scope')->except('index', 'create', 'duplicateIndex', 'edit', 'destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inertia::render('third-party-risk/questionnaires/Index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return Inertia::render('third-party-risk/questionnaires/Create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'bail',
                'required',
                'string',
                new UniqueQuestionnaire($request->version)
            ],
            'version' => [
                'bail',
                'required',
                'string'
            ]
        ]);

        $questionnaire = Questionnaire::create($request->only(['name', 'version']));

        Log::info('New questionnaire was added', ['questionnaire' => $questionnaire->id]);
        return redirect()->to(route('third-party-risk.questionnaires.questions.create', [$questionnaire->id]));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\ThirdPartyRisk\Questionnaire $questionnaire
     * @return \Illuminate\Http\Response
     */
    public function show(Questionnaire $questionnaire)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\ThirdPartyRisk\Questionnaire $questionnaire
     * @return \Illuminate\Http\Response
     */
    public function edit(Questionnaire $questionnaire)
    {
        abort_if($questionnaire->is_default, 403);
        return Inertia::render('third-party-risk/questionnaires/Edit', [
            'questionnaire' => $questionnaire
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ThirdPartyRisk\Questionnaire $questionnaire
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Questionnaire $questionnaire)
    {
        abort_if($questionnaire->is_default, 403);
        $request->validate([
            'name' => [
                'bail',
                'required',
                'string',
                new UniqueQuestionnaire($request->version, $questionnaire->id)
            ],
            'version' => [
                'bail',
                'required',
                'string'
            ]
        ]);

        $questionnaire->update($request->only(['name', 'version']));
        Log::info('Questionnaire was updated', ['questionnaire' => $questionnaire->id]);
        return redirect()->to(route('third-party-risk.questionnaires.index'))->withSuccess('Questionnaire updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ThirdPartyRisk\Questionnaire $questionnaire
     * @return \Illuminate\Http\Response
     */
    public function destroy(Questionnaire $questionnaire)
    {
        abort_if($questionnaire->is_default, 403);
        $questionnaire_id = $questionnaire->id;
        $questionnaire->delete();

        Log::info('New questionnaire was deleted', ['questionnaire' => $questionnaire_id]);
        return redirect()->back()->withSuccess('Questionnaire deleted successfully.');
    }

    public function getJsonData(Request $request)
    {
        $builder = Questionnaire::query();
        $per_page = 10;

        if ($request->has('search')) {
            $builder->where('name', 'LIKE', '%' . $request->search . '%');
        }

        if ($request->has('per_page')) {
            $per_page = $request->per_page;
        }

        $data = $builder->withCount('questions')->orderByDesc('id')->paginate($per_page);

        return response()->json(['data' => $data]);
    }

    public function duplicateIndex(Questionnaire $questionnaire)
    {
        $domains = Domain::all();
        return Inertia::render('third-party-risk/questionnaires/Duplicate', compact(
            'questionnaire',
            'domains'
        ));
    }

    public function duplicateStore(Questionnaire $questionnaire, Request $request)
    {
        $request->validate([
            'name' => [
                'bail',
                'required',
                'string',
                new UniqueQuestionnaire($request->version)
            ],
            'version' => [
                'bail',
                'required',
                'string'
            ]
        ]);

        $questions = $questionnaire->questions()->select(['text', 'domain_id'])->get()->toArray();
        $new_questionnaire = Questionnaire::create($request->only(['name', 'version']));
        $new_questionnaire->questions()->createMany($questions);

        Log::info("new questionnaire ($new_questionnaire->id) was created by duplicating an existing one ($questionnaire->id)", ['new_questionnaire' => $new_questionnaire->id]);
        return redirect()->to(route('third-party-risk.questionnaires.index'))->withSuccess('Questionnaire added successfully.');
    }
}
