<?php

use App\Mail\ThirdPartyRisk\Questionnaire as QuestionnaireMail;
use App\Models\ThirdPartyRisk\Project;
use App\Models\ThirdPartyRisk\ProjectEmail;
use Carbon\Carbon;
use Database\Seeders\Admin\AddNewAdminRoleSeeder;
use Database\Seeders\ThirdPartyRisk\DomainsSeeder;
use Database\Seeders\ThirdPartyRisk\IndustriesSeeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\Assert;
use function Pest\Laravel\get;

beforeEach(function () {
    // before each test, seed the role and industries
    // and login as a Third Party Risk Admin
    $this->seed([
        AddNewAdminRoleSeeder::class,
        IndustriesSeeder::class,
        DomainsSeeder::class,
    ]);

    $this->admin = loginWithRole('Third Party Risk Administrator');
    $this->data_scope = getScope($this->admin);
});

it('redirected to login if not logged in', function () {
    Auth::logout();
    get(route('third-party-risk.projects.index', ['data_scope' => $this->data_scope]))
        ->assertRedirect(route('login'));
});

it('can view projects page, and given props, if Global Admin or Third Party Risk Admin', function ($role) {
    $admin = loginWithRole($role);
    $data_scope = getScope($admin);

    if (in_array($role, ["Global Admin", "Third Party Risk Administrator"])) {
        $response = $this->get(route('third-party-risk.projects.index'));
        $response->assertOk();
        $response->assertInertia(function (Assert $page) {
            return $page
                ->component('third-party-risk/projects/Index')
                ->has('timezones')
                ->has('frequencies', 5);
        });
    } else {
        get(route('third-party-risk.projects.index', ['data_scope' => $this->data_scope]))->assertForbidden();
    }
})->with("roles");

it('shows the page with a project', function () {
    $project = Project::factory()->create();
    setScope($project, $this->data_scope);

    $this->get(route('third-party-risk.projects.index', ['data_scope' => $this->data_scope]))
        ->assertInertia(function (Assert $page) {
            return $page->component('third-party-risk/projects/Index');
        });

    $this->get(route('third-party-risk.projects.get-json-data', ['data_scope' => $this->data_scope]))
        ->assertJsonPath('projects.0.id', $project->id)
        ->assertJsonPath('projects.0.name', $project->name);
});

it('shows the page with an archived project', function () {
    $project = Project::factory()->create();
    $project->status = 'archived';
    $project->save();

    setScope($project, $this->data_scope);

    $this->get(route('third-party-risk.projects.index', ['data_scope' => $this->data_scope]))
        ->assertInertia(function (Assert $page) {
            return $page->component('third-party-risk/projects/Index');
        });

    $this->get(route('third-party-risk.projects.get-json-data', ['filter' => 'archived', 'data_scope' => $this->data_scope]))
        ->assertJsonPath('projects.0.id', $project->id)
        ->assertJsonPath('projects.0.name', $project->name);
});

it('can create project', function () {
    $project = Project::factory()->make();
    $this->followingRedirects();

    $this->from(route('third-party-risk.projects.index', ['data_scope' => $this->data_scope]))
        ->post(route('third-party-risk.projects.store', ['data_scope' => $this->data_scope]), $project->toArray())
        ->assertInertia(function (Assert $page) {
            return $page->where('flash.success', 'Project added successfully.');
        });

    $this->assertDatabaseHas("third_party_projects", ['name' => $project->name]);
});

it('returns error when wrong data is added to project', function ($project, $invalid_field) {
    $this->from(route('third-party-risk.projects.index', ['data_scope' => $this->data_scope]))
        ->post(route('third-party-risk.projects.store', ['data_scope' => $this->data_scope]), $project)
        ->assertRedirect(route('third-party-risk.projects.index', ['data_scope' => $this->data_scope]))
        ->assertSessionHasErrors($invalid_field);
})->with('wrong_projects_data');

it('can delete a project', function () {
    $project = Project::factory()->create();
    setScope($project, $this->data_scope);

    $this
        ->from(route('third-party-risk.projects.index', ['data_scope' => $this->data_scope]))
        ->delete(route('third-party-risk.projects.destroy', [$project->id, 'data_scope' => $this->data_scope]))
        ->assertRedirect(route('third-party-risk.projects.index', ['data_scope' => $this->data_scope]))
        ->assertSessionHas('success', 'Project deleted successfully.');
    $this->assertDatabaseMissing('third_party_projects', ['name' => $project->name]);

});

it('reactivates based on frequency and sends email', function () {
    $project = Project::factory()->state([
        'status' => 'archived',
        'frequency' => "Weekly",
        'launch_date' => $this->faker()->dateTimeBetween('-6 weeks', '-4 weeks', 'CET'),
        'due_date' => $this->faker()->dateTimeBetween('-3 weeks', '-2 weeks', 'CET'),
    ])->create();

    $this->artisan('schedule:run');

    $this->assertDatabaseHas("third_party_project_activities", [
        'type' => "new-cycle",
        'project_id' => $project->id
    ]);

    // check that the project data was updated, and the project is reactivated
    $project = $project->refresh();

    $this->assertDatabaseHas('third_party_projects', [
        'id' => $project->id,
        'status' => $project->status,
    ]);
});

it('runs cron job and send email for project', function () {
    Mail::fake();

    $project = Project::factory()->in_progress()->create();
    ProjectEmail::create([
        'project_id' => $project->id,
        'token' => encrypt($project->id . '-' . $project->vendor_id . date('r', time())),
    ]);

    $this->artisan('schedule:run');

    $email = $project->vendor->email;
    Mail::assertSent(QuestionnaireMail::class, function ($mail) use ($email, $project) {
        return $mail->hasTo($email);
    });
});

dataset("wrong_projects_data", function () {
    $projects = [];
    $project = [
        "name" => "Project 56",
        "questionnaire_id" => 2,
        "vendor_id" => 2,
        "launch_date" => "2022-01-20 11:02:20",
        "due_date" => "2022-02-20 11:02:20",
        "timezone" => "Europe/Bucharest",
        "frequency" => "Annually",
    ];

    // value not available, name;
    $project1 = $project;
    unset($project1['name']);
    $projects[] = [
        "data" => $project1,
        "filed" => ['name']
    ];

    // value too long
    $project2 = $project;
    $project2['name'] = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nunc convallis ipsum sit amet arcu placerat fringilla. Nullam eu laoreet orci. Suspendisse semper blandit pulvinar. Duis in mattis metus nulla.";
    $projects[] = [
        "data" => $project2,
        "field" => ['name']
    ];

    // launch date is before now
    $project3 = $project;
    $project3['launch_date'] = "1992-01-20 11:02:20";
    $projects[] = [
        "data" => $project3,
        "field" => ['launch_date']
    ];

    // due date is before launch date
    $project4 = $project;
    $project4['due_date'] = "2022-01-00 12:00:00";
    $projects[] = [
        "data" => $project4,
        "field" => ['due_date']
    ];

    // vendor_id is wrong
    $project5 = $project;
    $project5['vendor_id'] = 99999;
    $projects[] = [
        "data" => $project5,
        "field" => ['vendor_id']
    ];

    // questionnaire_id is wrong
    $project6 = $project;
    $project6['questionnaire_id'] = 99999;
    $projects[] = [
        "data" => $project6,
        "field" => ['questionnaire_id']
    ];

    // timezone is wrong
    $project7 = $project;
    $project7['timezone'] = "GMT";
    $projects[] = [
        "data" => $project7,
        "field" => ['timezone']
    ];

    // frequency is wrong
    $project8 = $project;
    $project8['frequency'] = "Daily";
    $projects[] = [
        "data" => $project8,
        "field" => ['frequency']
    ];

    return $projects;
});


// test to see if timezone list is correct. Commented out as it will run 117 times and is not needed.
//it('has correct timezone', function ($timezone) {
//    loginWithRole('Third Party Risk Administrator');
//
//    $project = Project::factory()->state([
//        'timezone' => $timezone,
//    ])->create();
//
//    $this->get(route('third-party-risk.projects.index'))
//        ->assertInertia(function (Assert $page) {
//            return $page->component('third-party-risk/projects/Index');
//        });
//
//    $this->get(route('third-party-risk.projects.get-json-data'))
//        ->assertJsonPath('projects.0.id', $project->id)
//        ->assertJsonPath('projects.0.name', $project->name);
//})->with('timezones');
