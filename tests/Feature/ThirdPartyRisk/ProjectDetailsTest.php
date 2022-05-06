<?php

use App\Mail\ThirdPartyRisk\ProjectReminder;
use App\Models\ThirdPartyRisk\Project;
use App\Models\ThirdPartyRisk\ProjectEmail;
use App\Models\ThirdPartyRisk\Question;
use Database\Seeders\Admin\AddNewAdminRoleSeeder;
use Database\Seeders\ThirdPartyRisk\DomainsSeeder;
use Database\Seeders\ThirdPartyRisk\IndustriesSeeder;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\Assert;
use function Pest\Laravel\post;

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

it('can view project details page only if global admin or third party risk admin', function ($role) {
    $admin = loginWithRole($role);
    $data_scope = getScope($admin);

    $project = Project::factory()->create();
    setScope($project, $this->data_scope);

    if (in_array($role, ["Global Admin", "Third Party Risk Administrator"])) {
        $response = $this->get(route('third-party-risk.projects.show', $project->id));
        $response->assertOk();
        $response->assertInertia(function (Assert $page) use ($project) {
            return $page
                ->component('third-party-risk/projects/Show')
                ->has('project.questionnaire')
                ->has('project.vendor');
        });
    } else {
        $this->get(route('third-party-risk.projects.show', $project->id))
            ->assertForbidden();
    }
})->with('roles');

it("can view project details and questions data", function () {
    $project = Project::factory()->create();
    setScope($project, $this->data_scope);
    $questionnaire = $project->questionnaire;
    setScope($questionnaire, $this->data_scope);

    $questions = Question::where('questionnaire_id', $project->questionnaire_id)->get();

    $this->get(route('third-party-risk.projects.show', [$project->id]))
        ->assertInertia(function (Assert $page) use ($project) {
            return $page->component('third-party-risk/projects/Show');
        });

    $this->get(route('third-party-risk.projects.get-project-answers', [$project->id, 'data_scope' => $this->data_scope]))
        ->assertJsonPath("data.total", $questions->count());
});

it('sends reminder for overdue and in progress projects', function () {
    $projects = collect();

    $overdue_project = Project::factory()->overdue()->create();
    $overdue_project_vendor = $overdue_project->vendor;
    setScope($overdue_project, $this->data_scope);
    setScope($overdue_project_vendor, $this->data_scope);

    $projects->push($overdue_project);

    $in_progress_project = Project::factory()->overdue()->create();
    $in_progress_project_vendor = $in_progress_project->vendor;
    setScope($in_progress_project, $this->data_scope);
    setScope($in_progress_project_vendor, $this->data_scope);


    $projects->push($in_progress_project);

    foreach ($projects as $project) {
        Mail::fake();

        ProjectEmail::create([
            'project_id' => $project->id,
            'token' => encrypt($project->id . '-' . $project->vendor_id . date('r', time())),
        ]);

        $this->from(route('third-party-risk.projects.show', [$project->id, 'data_scope' => $this->data_scope]))
            ->post(route('third-party-risk.projects.send-project-reminder', [$project->id, 'data_scope' => $this->data_scope]), [])
            ->assertRedirect(route('third-party-risk.projects.show', [$project->id, 'data_scope' => $this->data_scope]))
            ->assertSessionHas('success', 'Third party project reminder email sent to vendor.');

        $this->assertDatabaseHas("third_party_project_activities", [
            'type' => "reminder-email-sent",
            'project_id' => $project->id
        ]);

        Mail::assertSent(ProjectReminder::class);
    }
});

it('doesn\'t send reminder for completed or Not started projects', function () {
    $projects = collect();

    $not_stated_project = Project::factory()->not_started()->create();
    setScope($not_stated_project, $this->data_scope);

    $projects->push($not_stated_project);

    $completed_project = Project::factory()->completed()->create();
    setScope($completed_project, $this->data_scope);

    $projects->push($completed_project);

    foreach ($projects as $project) {
        ProjectEmail::create([
            'project_id' => $project->id,
            'token' => encrypt($project->id . '-' . $project->vendor_id . date('r', time())),
        ]);

        $this->from(route('third-party-risk.projects.show', [$project->id]))
            ->post(route('third-party-risk.projects.send-project-reminder', [$project->id]), [])
            ->assertRedirect(route('third-party-risk.projects.show', [$project->id]))
            ->assertSessionHasErrors();
    }
});

it('doesn\'t send reminder because it does not have a vendor email attached', function () {
    $overdue_project = Project::factory()->overdue()->create();
    setScope($overdue_project, $this->data_scope);

    $this->from(route('third-party-risk.projects.show', [$overdue_project->id]))
        ->post(route('third-party-risk.projects.send-project-reminder', [$overdue_project->id]), [])
        ->assertRedirect(route('third-party-risk.projects.show', [$overdue_project->id]))
        ->assertSessionHasErrors();

    $this->assertDatabaseHas("third_party_project_activities", [
        'type' => "reminder-email-error",
        'project_id' => $overdue_project->id
    ]);

});

it('doesn\'t send reminder because it fails to send the email', function () {
    $overdue_project = Project::factory()->overdue()->create();
    setScope($overdue_project, $this->data_scope);

    ProjectEmail::create([
        'project_id' => $overdue_project->id,
        'token' => null,
    ]);

    $this->from(route('third-party-risk.projects.show', [$overdue_project->id]))
        ->post(route('third-party-risk.projects.send-project-reminder', [$overdue_project->id]), [])
        ->assertRedirect(route('third-party-risk.projects.show', [$overdue_project->id]))
        ->assertSessionHas('exception', 'Failed to process request. Please check SMTP authentication connection.');

    $this->assertDatabaseHas("third_party_project_activities", [
        'type' => "reminder-email-error",
        'project_id' => $overdue_project->id
    ]);

});

it('can export csv', function () {
    $project = Project::factory()->create();
    setScope($project, $this->data_scope);
    $questionnaire = $project->questionnaire;
    setScope($questionnaire, $this->data_scope);
    $vendor = $project->vendor;
    setScope($vendor, $this->data_scope);

    $this->get(route('third-party-risk.projects.export-csv', [$project->id, 'data_scope' => $this->data_scope]))
        ->assertDownload("project-details.csv");
});

it('can export pdf', function () {
    $project = Project::factory()->create();
    setScope($project, $this->data_scope);
    $questionnaire = $project->questionnaire;
    setScope($questionnaire, $this->data_scope);
    $vendor = $project->vendor;
    setScope($vendor, $this->data_scope);


    $this->get(route('third-party-risk.projects.export-pdf', [$project->id, 'data_scope' => $this->data_scope]))
        ->assertOk();
});
