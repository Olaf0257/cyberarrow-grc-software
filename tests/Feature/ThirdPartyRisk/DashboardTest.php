<?php

use App\Models\ThirdPartyRisk\Project;
use Database\Seeders\Admin\AddNewAdminRoleSeeder;
use Database\Seeders\ThirdPartyRisk\DomainsSeeder;
use Database\Seeders\ThirdPartyRisk\IndustriesSeeder;
use Inertia\Testing\Assert;

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

it('renders dashboard page', function ($role) {
    loginWithRole($role);

    if (in_array($role, ["Global Admin", "Third Party Risk Administrator"])) {
        $response = $this->get(route('third-party-risk.dashboard'));
        $response->assertOk();
        $response->assertInertia(function (Assert $page){
            return $page
                ->component('third-party-risk/dashboard/Index');
        });
    } else {
        $this->get(route('third-party-risk.dashboard'))->assertForbidden();
    }

})->with('roles');

it('gets vendors data ', function () {
    $projects = Project::factory()->in_progress()->count(2)->create();
    foreach ($projects as $project) {
        setScope($project, $this->data_scope);
    }

        $this->get(route('third-party-risk.dashboard.get-vendors-data', ['data_scope' => $this->data_scope]))
        ->assertOk()
        ->assertJsonCount(2)
        ->assertJsonFragment([
            'level' => 1,
            'color' => "#ff0000",
            'name' => "Level 1",
        ])
        ->assertJsonPath('projects_progress.In Progress', $projects->count());
});

it('gets top vendors data', function () {
    $projects = Project::factory()->in_progress()->count(100)->create();
    $vendors_count = 0;
    foreach ($projects as $project) {
        setScope($project, $this->data_scope);
        $vendor = $project->vendor;
        setScope($vendor, $this->data_scope);
        $vendors_count++;
    }

    $this->get(route('third-party-risk.dashboard.get-top-vendors', ['data_scope' => $this->data_scope]))
        ->assertOk()
        ->assertJsonPath('data.total', $vendors_count);

});

it('can export pdf', function () {
    $projects = Project::factory()->count(100)->create();
    foreach ($projects as $project) {
        setScope($project, $this->data_scope);
    }

    $this->get(route('third-party-risk.dashboard.export-pdf', ['data_scope' => $this->data_scope]))
        ->assertOk();
});
