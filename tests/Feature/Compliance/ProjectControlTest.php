<?php

use App\Models\Compliance\Evidence;
use App\Models\Compliance\Project;
use App\Models\Compliance\ProjectControl;
use App\Models\Compliance\Standard;
use App\Models\UserManagement\Admin;
use Database\Seeders\Compliance\DefaultComplianceStandardsSeeder;
use Inertia\Testing\Assert;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;
use function Pest\Laravel\seed;

beforeEach(function () {
    $this->followingRedirects();

    seed([
        DefaultComplianceStandardsSeeder::class
    ]);
});

it('can upload evidence as responsible', function () {
    $user = Admin::factory()->create();
    $responsible = loginWithRole('Compliance Administrator');

    $standard = Standard::inRandomOrder()->first();

    $project = Project::factory()->create([
        'standard_id' => $standard->id,
        'standard' => $standard->name
    ]);

    $controls = $standard->controls()->get(['name', 'primary_id', 'sub_id', 'id_separator', 'description', 'required_evidence'])->toArray();

    /*Creating project controls*/
    $project->controls()->createMany($controls);

    $projectControl = ProjectControl::first();

    $projectControl->update(['approver' => $user->id, 'responsible' => $responsible->id, 'deadline' => date("Y-m-d", strtotime("+1 day"))]);

    $data = Evidence::factory()->make(['project_control_id' => $projectControl->id]);
    $data->active_tab = 'text-input';

    $this->from(route('compliance-dashboard'))->post(route('compliance-project-control-evidences-upload', ['project' => $project->id, 'projectControl' => $projectControl->id]), $data->toArray())
        ->assertInertia(function (Assert $page) {
            return $page
                ->component('compliance/dashboard/Dashboard')
                ->where('flash.success', 'Evidence successfully uploaded');
        });
});

it('can comment on project controls as responsible', function () {
    $user = Admin::factory()->create();
    $responsible = loginWithRole('Compliance Administrator');

    $standard = Standard::inRandomOrder()->first();

    $project = Project::factory()->create([
        'standard_id' => $standard->id,
        'standard' => $standard->name
    ]);

    $controls = $standard->controls()->get(['name', 'primary_id', 'sub_id', 'id_separator', 'description', 'required_evidence'])->toArray();

    /*Creating project controls*/
    $project->controls()->createMany($controls);

    $projectControl = ProjectControl::first();

    $projectControl->update(['approver' => $user->id, 'responsible' => $responsible->id, 'deadline' => date("Y-m-d", strtotime("+1 day"))]);

    post(route('compliance.project-controls-comments', ['project' => $project->id, 'projectControl' => $projectControl->id]), ['comment' => 'Test Comment']);

    assertDatabaseHas('compliance_project_control_comments', [
        'project_control_id' => $projectControl->id,
        'from' => $responsible->id,
        'to' => $user->id,
        'comment' => 'Test Comment'
    ]);
});

