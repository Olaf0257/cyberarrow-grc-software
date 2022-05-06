<?php

use App\Models\Compliance\Project;
use App\Models\Compliance\Standard;
use Database\Seeders\Compliance\DefaultComplianceStandardsSeeder;
use Illuminate\Testing\Fluent\AssertableJson;
use Inertia\Testing\Assert;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\get;
use function Pest\Laravel\getJson;
use function Pest\Laravel\seed;

beforeEach(function () {
    $this->followingRedirects();
});

it('cannot access global dashboard page when unauthenticated', function () {
    get(route('global.dashboard'))
        ->assertInertia(function (Assert $page) {
            return $page->component('auth/LoginPage');
        });
});

it('can access global dashboard page when authenticated', function () {
    loginWithRole();

    get(route('global.dashboard'))
        ->assertOk()
        ->assertInertia(function (Assert $page) {
            return $page->component('global-dashboard/GlobalDashboard');
        });
});

it('can view global dashboard page only by Global Admin and Compliance Administrator role', function ($role) {
    loginWithRole($role);

    if (in_array($role, ['Global Admin', 'Compliance Administrator'])) {
        get(route('global.dashboard'))
            ->assertOk()
            ->assertInertia(function (Assert $page) {
                return $page->component('global-dashboard/GlobalDashboard');
            });
    } else {
        get(route('global.dashboard'))
            ->assertForbidden();
    }
})->with('roles');

it('shows only total count of assigned controls in controls status of dashboard', function () {
    $admin = loginWithRole();
    $data_scope = getScope($admin);

    seed(DefaultComplianceStandardsSeeder::class);

    $standard = Standard::inRandomOrder()->first();

    $project = Project::factory()->create([
        'standard_id' => $standard->id,
        'standard' => $standard->name
    ]);

    $controls = $standard->controls()->get(['name', 'primary_id', 'sub_id', 'id_separator', 'description', 'required_evidence'])->toArray();

    /*Creating project controls*/
    $project->controls()->createMany($controls);

    assertDatabaseCount('compliance_projects', 1);

    $totalControls = $project->controls()->count();

    getJson(route('global.dashboard.get-data', ['data_scope' => $data_scope, 'projects' => $project->id]))->assertJson(fn(AssertableJson $json) => $json
        ->where('success', true)
        ->where('data.allControls', $totalControls)
        ->where('data.notImplementedControls', $totalControls)
    );
});
