<?php

use \App\Models\ThirdPartyRisk\Questionnaire;
use \App\Models\ThirdPartyRisk\Question;
use \App\Models\ThirdPartyRisk\Domain;
use \Illuminate\Http\UploadedFile;
use \Maatwebsite\Excel\Facades\Excel;
use \Illuminate\Support\Facades\Storage;
use \App\Imports\QuestionsImport;
use \Inertia\Testing\Assert;

beforeEach(function () {
    $this->seed([
        \Database\Seeders\Admin\AddNewAdminRoleSeeder::class,
        \Database\Seeders\ThirdPartyRisk\DomainsSeeder::class
    ]);
    $this->followingRedirects();
    loginWithRole('Third Party Risk Administrator');
    $this->questionnaire = Questionnaire::factory()->has(Question::factory()->count(3)->for(Domain::first()))->create();

    $this->admin = loginWithRole('Third Party Risk Administrator');
    $this->data_scope = getScope($this->admin);
    setScope($this->questionnaire, $this->data_scope);
});

//it('shows the questionnaires page with one questionnaire', function () {
//    $this
//        ->get(route('third-party-risk.questionnaires.index'))
//        ->assertOk()
//        ->assertInertia(function (Assert $page) {
//            return $page->component('third-party-risk/questionnaires/Index');
//        });
//    $this
//        ->get(route('third-party-risk.questionnaires.get-json-data', ['data_scope' => $this->data_scope]))
//        ->assertJsonPath('data.data.0.name', $this->questionnaire->name)
//        ->assertJsonPath('data.data.0.questions_count', $this->questionnaire->questions->count());
//});
//
//it('allows the admin to create a new questionnaire and redirects him to the page where he can add questions', function () {
//    $questionnaire = Questionnaire::factory()->make();
//    $this->post(route('third-party-risk.questionnaires.store', ['data_scope' => $this->data_scope]), $questionnaire->toArray())
//        ->assertInertia(function (Assert $page) use ($questionnaire) {
//            return $page
//                ->component('third-party-risk/questions/Create')
//                ->has('domains', Domain::count())
//                ->has('questionnaire');
//        });
//});
//
//it('allows the admin to add a question to a questionnaire and displays a success message when the data is valid', function () {
//    $questions_count = $this->questionnaire->questions->count();
//    $this->post(route('third-party-risk.questionnaires.questions.store', [$this->questionnaire->id]), [
//        'text' => 'My new question.',
//        'domain_id' => Domain::first()->id
//    ])
//        ->assertInertia(function (Assert $page) {
//            return $page
//                ->component('third-party-risk/questions/Index')
//                ->where('flash.success', 'Question added successfully.');
//        });
//
//    $this->get(route('third-party-risk.questionnaires.questions.get-json-data', [$this->questionnaire->id, 'data_scope' => $this->data_scope]))
//        ->assertJsonPath('data.total', $questions_count + 1);
//});
//
//it('doesn\'t allow the admin to add a question to a default questionnaire', function () {
//    $questionnaire = Questionnaire::factory()->isDefault()->create();
//
//    $this->get(route('third-party-risk.questionnaires.questions.create', [$questionnaire->id]))->assertForbidden();
//    $this->post(route('third-party-risk.questionnaires.questions.store', [$questionnaire->id]), [
//        'text' => 'My new question.',
//        'domain_id' => Domain::first()->id
//    ])
//        ->assertForbidden();
//});
//
//it('allows the admin to delete a question and returns a success message', function () {
//    $question_id = $this->questionnaire->questions->first()->id;
//    $this
//        ->from(route('third-party-risk.questionnaires.questions.index', $this->questionnaire->id))
//        ->delete(route('third-party-risk.questionnaires.questions.destroy', [$this->questionnaire->id, $question_id]))
//        ->assertInertia(function (Assert $page) {
//            return $page
//                ->component('third-party-risk/questions/Index')
//                ->where('flash.success', 'Question deleted successfully.');
//        });
//    $this->assertDatabaseMissing('third_party_questions', ['id' => $question_id]);
//});
//
//it('doesn\'t allow the admin to delete a question that belongs to a default questionnaire', function () {
//    $questionnaire = Questionnaire::factory()->isDefault()->has(Question::factory()->count(3)->for(Domain::first()))->create();
//    $this->delete(route('third-party-risk.questionnaires.questions.destroy', [$questionnaire->id, $questionnaire->questions->first()->id]))
//        ->assertForbidden();
//});
//
//it('allows the admin to delete a questionnaire', function () {
//    $this
//        ->from(route('third-party-risk.questionnaires.index'))
//        ->delete(route('third-party-risk.questionnaires.destroy', $this->questionnaire->id))
//        ->assertInertia(function (Assert $page) {
//            return $page
//                ->component('third-party-risk/questionnaires/Index')
//                ->where('flash.success', 'Questionnaire deleted successfully.');
//        });
//    $this->assertDatabaseMissing('third_party_questionnaires', ['id' => $this->questionnaire->id]);
//});
//
//it('doesn\'t allow the admin to delete a default questionnaire', function () {
//    $questionnaire = Questionnaire::factory()->isDefault()->has(Question::factory()->count(3)->for(Domain::first()))->create();
//    $this
//        ->from(route('third-party-risk.questionnaires.index'))
//        ->delete(route('third-party-risk.questionnaires.destroy', $questionnaire->id))
//        ->assertForbidden();
//});
//
//it('allows the admin to edit a questionnaire and returns a success message', function () {
//    $data = $this->questionnaire->toArray();
//    $data['name'] = 'My new name';
//    $data['data_scope'] = $this->data_scope;
//
//    $this
//        ->from(route('third-party-risk.questionnaires.index'))
//        ->put(route('third-party-risk.questionnaires.update', $this->questionnaire->id), $data)
//        ->assertInertia(function (Assert $page) {
//            return $page
//                ->component('third-party-risk/questionnaires/Index')
//                ->where('flash.success', 'Questionnaire updated successfully.');
//        });
//    $this->assertDatabaseHas('third_party_questionnaires', ['name' => 'My new name']);
//});

it('doesn\'t allow the admin to edit a default questionnaire', function () {
    $questionnaire = Questionnaire::factory()->isDefault()->create();
    setScope($questionnaire, $this->data_scope);

    $this->get(route('third-party-risk.questionnaires.edit', $questionnaire->id))->assertForbidden();
    $this
        ->from(route('third-party-risk.questionnaires.index'))
        ->put(route('third-party-risk.questionnaires.update', [$questionnaire->id, 'data_scope' => $this->data_scope]), $questionnaire->toArray())
        ->assertForbidden();
});

it('allows the admin to duplicate a questionnaire', function () {
    $this
        ->post(route('third-party-risk.questionnaires.duplicate.store', $this->questionnaire->id), [
            'name' => 'My Questionnaire',
            'version' => 'v1',
            'data_scope' => $this->data_scope
        ])
        ->assertInertia(function (Assert $page) {
            return $page
                ->component('third-party-risk/questionnaires/Index')
                ->where('flash.success', 'Questionnaire added successfully.');
        });

    $this
        ->get(route('third-party-risk.questionnaires.get-json-data', ['data_scope' => $this->data_scope]))
        ->assertJsonPath('data.data.0.name', 'My Questionnaire')
        ->assertJsonPath('data.data.0.questions_count', $this->questionnaire->questions->count());
});

it('shows the questions page with 3 questions', function () {
    $this
        ->get(route('third-party-risk.questionnaires.questions.index', $this->questionnaire->id))
        ->assertInertia(function (Assert $page) {
            return $page
                ->component('third-party-risk/questions/Index')
                ->has('questionnaire');
        });
    $this
        ->get(route('third-party-risk.questionnaires.questions.get-json-data', [$this->questionnaire->id, 'data_scope' => $this->data_scope]))
        ->assertJsonPath('data.total', 3);
});

it('allows the admin to edit a question', function () {
    $question = $this->questionnaire->questions->first();

    $data = $question->toArray();
    $data['text'] = 'My new question';

    $this
        ->put(route('third-party-risk.questionnaires.questions.update', [$this->questionnaire->id, $question->id]), $data)
        ->assertInertia(function (Assert $page) {
            return $page
                ->component('third-party-risk/questions/Index')
                ->where('flash.success', 'Question updated successfully.');
        });
    $this->assertDatabaseHas('third_party_questions', ['text' => 'My new question']);
});

it('doesn\'t allow the admin to edit a question that belongs to a default questionnaire', function () {
    $questionnaire = Questionnaire::factory()->isDefault()->has(Question::factory()->count(3)->for(Domain::first()))->create();
    $this->get(route('third-party-risk.questionnaires.questions.edit', [$questionnaire->id, $questionnaire->questions->first()->id]))->assertForbidden();
    $this
        ->put(route('third-party-risk.questionnaires.questions.update', [$questionnaire->id, $questionnaire->questions->first()->id]), $questionnaire->toArray())
        ->assertForbidden();
});

it('shouldn\'t allow the admin to add an invalid questionnaire', function ($invalidQuestionnaire, $invalidField) {
    $this
        ->from(route('third-party-risk.questionnaires.index'))
        ->post(route('third-party-risk.questionnaires.store'), $invalidQuestionnaire)
        ->assertInertia(function (Assert $page) use ($invalidField) {
            return $page
                ->component('third-party-risk/questionnaires/Index')
                ->has('errors', count($invalidField));
        });
})->with([
    [
        function () {
            return [
                'name' => $this->questionnaire->name,
                'version' => $this->questionnaire->version,
                'data_scope' => $this->data_scope,
            ];
        },
        ['name']
    ],
    [
        function(){
            return [
                'name' => '',
                'version' => '',
                'data_scope' => $this->data_scope,
            ];
        },
        ['name', 'version']
    ],
    [
        function(){
            return [
                'name' => 'My Questionnaire',
                'version' => '',
                'data_scope' => $this->data_scope,
            ];
        },
        ['version']
    ],
    [
        function(){
            return [
                'name' => '',
                'version' => 'v1',
                'data_scope' => $this->data_scope,
            ];
        },
        ['name']
    ]
]);

it('shouldn\'t allow to edit a questionnaire with invalid data', function ($invalidQuestionnaire, $invalidField) {
    $questionnaire = Questionnaire::factory()->create();
    setScope($questionnaire, $this->data_scope);

    $this
        ->from(route('third-party-risk.questionnaires.edit', $questionnaire->id))
        ->put(route('third-party-risk.questionnaires.update', $questionnaire->id), $invalidQuestionnaire)
        ->assertInertia(function (Assert $page) use ($invalidField) {
            return $page->has('errors', count($invalidField));
        });
})->with([
    [
        function(){
            return [
                'name' => '',
                'version' => '',
                'data_scope' => $this->data_scope,
            ];
        },
        ['name', 'version']
    ],
    [
        function(){
            return [
                'name' => 'My questionnaire',
                'data_scope' => $this->data_scope,
            ];
        },
        ['version']
    ],
    [
        function(){
            return [
                'version' => 'v1',
                'data_scope' => $this->data_scope,
            ];
        },
        ['name']
    ],
    [
        function () {
            return [
                'name' => $this->questionnaire->name,
                'version' => $this->questionnaire->version,
                'data_scope' => $this->data_scope,
            ];
        },
        ['name']
    ]
]);

it('shouldn\'t allow to duplicate a questionnaire with invalid data', function ($invalidQuestionnaire, $invalidField) {
    $questionnaire = Questionnaire::factory()->create();
    setScope($questionnaire, $this->data_scope);
    $this
        ->from(route('third-party-risk.questionnaires.duplicate.index', $questionnaire->id))
        ->post(route('third-party-risk.questionnaires.duplicate.store', $questionnaire->id), $invalidQuestionnaire)
        ->assertInertia(function (Assert $page) use ($invalidField) {
            return $page->has('errors', count($invalidField));
        });
})->with([
    [
        function () {
            return [
                'name' => '',
                'version' => '',
                'data_scope' => $this->data_scope,
            ];
        },
        ['name', 'version']
    ],
    [
        function () {
            return [
                'name' => 'My questionnaire',
                'data_scope' => $this->data_scope,
            ];
        },
        ['version']
    ],
    [
        function () {
            return [
                'version' => 'v1',
                'data_scope' => $this->data_scope,
            ];
        },

        ['name']
    ],
    [
        function () {
            return [
                'name' => $this->questionnaire->name,
                'version' => $this->questionnaire->version,
                'data_scope' => $this->data_scope,
            ];
        },
        ['name']
    ]
]);

it('shouldn\'t allow to add an invalid question', function ($invalidQuestion, $invalidField) {
    $this
        ->from(route('third-party-risk.questionnaires.questions.index', $this->questionnaire->id))
        ->post(route('third-party-risk.questionnaires.questions.store', $this->questionnaire->id), $invalidQuestion)
        ->assertInertia(function (Assert $page) use ($invalidField) {
            return $page->has('errors', count($invalidField));
        });
})->with([
    [
        function () {
            return [
                'text' => $this->questionnaire->questions->first()->text,
                'domain_id' => $this->questionnaire->questions->first()->domain_id,
                'data_scope' => $this->data_scope,
            ];
        },
        ['text']
    ],
    [
        function () {
            return [
                'text' => '',
                'domain_id' => '',
                'data_scope' => $this->data_scope,
            ];
        },
        ['text', 'domain_id']
    ],
    [
        function () {
            return [
                'text' => 'My question',
                'data_scope' => $this->data_scope,
            ];
        },
        ['domain_id']
    ],
    [
        function () {
            return [
                'text' => 'My question',
                'domain_id' => 0,
                'data_scope' => $this->data_scope,
            ];
        },
        ['domain_id']
    ]
]);


it('shouldn\'t allow to edit a question with invalid data', function ($invalidQuestion, $invalidField) {
    $this
        ->from(route('third-party-risk.questionnaires.questions.edit', [$this->questionnaire->id, $this->questionnaire->questions->last()->id]))
        ->put(route('third-party-risk.questionnaires.questions.update', [$this->questionnaire->id, $this->questionnaire->questions->last()->id]), $invalidQuestion)
        ->assertInertia(function (Assert $page) use ($invalidField) {
            return $page->has('errors', count($invalidField));
        });
})->with([
    [
        function () {
            return [
                'text' => $this->questionnaire->questions->first()->text,
                'domain_id' => $this->questionnaire->questions->first()->domain_id,
                'data_scope' => $this->data_scope,
            ];
        },
        ['text']
    ],
    [
        function () {
            return [
                'text' => '',
                'domain_id' => '',
                'data_scope' => $this->data_scope,
            ];
        },
        ['text', 'domain_id']
    ],
    [
        function () {
            return [
                'text' => 'My question',
                'data_scope' => $this->data_scope,
            ];
        },

        ['domain_id']
    ],
    [
        function () {
            return [
                'text' => 'My question',
                'domain_id' => 0,
                'data_scope' => $this->data_scope,
            ];
        },
        ['domain_id']
    ]
]);

it('should download a questions sample', function () {
    Excel::fake();
    $this
        ->get(route('third-party-risk.questionnaires.questions.download-sample'));
    Excel::assertDownloaded('3rd-party-risks-sample.csv');
});

it('shouldn\'t allow to upload invalid questions csv file', function ($file) {
    // Each file has an invalid cell
    Storage::fake('local');
    $this
        ->from(route('third-party-risk.questionnaires.questions.create', $this->questionnaire->id))
        ->post(route('third-party-risk.questionnaires.questions.batch-import', $this->questionnaire->id), [
            'csv_file' => $file
        ])
        ->assertInertia(function (Assert $page) {
            return $page
                ->component('third-party-risk/questions/Create')
                ->has('errors', 1);
        });

})->with([
    function () {
        return new UploadedFile(base_path('tests/Feature/ThirdPartyRisk/files/imports/bad-import1.csv'), 'bad-import1.csv', 'text/csv', null, true);
    },
    function () {
        return new UploadedFile(base_path('tests/Feature/ThirdPartyRisk/files/imports/bad-import2.csv'), 'bad-import2.csv', 'text/csv', null, true);
    },
    function () {
        return new UploadedFile(base_path('tests/Feature/ThirdPartyRisk/files/imports/bad-import3.csv'), 'bad-import3.csv', 'text/csv', null, true);
    }
]);

it('should import questions from csv file', function () {
    Storage::fake('local');
    $file = new UploadedFile(base_path('tests/Feature/ThirdPartyRisk/files/imports/good-import.csv'), 'good-import.csv', 'text/csv', null, true);
    $this
        ->from(route('third-party-risk.questionnaires.questions.create', $this->questionnaire->id))
        ->post(route('third-party-risk.questionnaires.questions.batch-import', $this->questionnaire->id), [
            'csv_file' => $file
        ])
        ->assertInertia(function (Assert $page) {
            return $page
                ->component('third-party-risk/questions/Index')
                ->where('flash.success', 'Questions imported successfully');
        });
});
