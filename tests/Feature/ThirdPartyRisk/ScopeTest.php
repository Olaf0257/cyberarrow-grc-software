<?php

use App\Models\ThirdPartyRisk\Industry;
use App\Models\ThirdPartyRisk\Project;
use App\Models\ThirdPartyRisk\Questionnaire;
use App\Models\ThirdPartyRisk\Vendor;
use Database\Seeders\Admin\AddNewAdminRoleSeeder;
use Database\Seeders\ThirdPartyRisk\DomainsSeeder;
use Database\Seeders\ThirdPartyRisk\IndustriesSeeder;
use \App\Models\ThirdPartyRisk\Question;
use Inertia\Testing\Assert;

beforeEach(function () {
// before each test, seed the role and industries
// and login as a Third Party Risk Admin
    $this->seed([
        AddNewAdminRoleSeeder::class,
        IndustriesSeeder::class,
        DomainsSeeder::class,
    ]);

    // create a base scoped vendor. There will always be and extra questionnaire in a different department
    $created_data = createBaseData([Vendor::class, Questionnaire::class, Project::class]);

    // use for checks, example:
    // $this->vendor_data = $created_data[Vendor::class];
    // $this->assertDatabaseHas("third_party_vendors", ['name' => $this->vendor_data["collection"]->name]);

});

it('can only see vendor created in it\'s own department', function () {

    $admin = loginWithRole('Third Party Risk Administrator');
    $data_scope = getScope($admin);

    $vendor = Vendor::factory()->for(Industry::first())->create();
    setScope($vendor, $data_scope);

    $this->assertDatabaseHas("third_party_vendors", ['name' => $vendor['name']]);

    // the api returns data
    $this->get(route('third-party-risk.vendors.get-json-data', ['data_scope' => $data_scope]))
        ->assertJsonPath('data.data.0.name', $vendor['name'])
        ->assertJsonCount(1, 'data.data');
});

it('can only see questionnaire created in  it\'s own department', function () {
    $admin = loginWithRole('Third Party Risk Administrator');
    $data_scope = getScope($admin);

    $questionnaire = Questionnaire::factory()->create();
    setScope($questionnaire, $data_scope);

    $this
        ->get(route('third-party-risk.questionnaires.get-json-data', ['data_scope' => $data_scope]))
        ->assertJsonPath('data.data.0.name', $questionnaire['name'])
        ->assertJsonCount(1, 'data.data');
});

it('can only see project created in it\'s own department', function () {
    $admin = loginWithRole('Third Party Risk Administrator');
    $data_scope = getScope($admin);

    $project = Project::factory()->create();
    setScope($project, $data_scope);

    $this->assertDatabaseHas("third_party_projects", ['name' => $project['name']]);

    // the api returns data
    $this->get(route('third-party-risk.projects.get-json-data', ['data_scope' => $data_scope]))
        ->assertJsonPath('projects.0.name', $project['name'])
        ->assertJsonCount(1, 'projects');
});

it('can only see the vendor maturity data for it\'s own department', function () {
    $admin = loginWithRole('Third Party Risk Administrator');
    $data_scope = getScope($admin);

    $project = Project::factory()->in_progress()->create();
    setScope($project, $data_scope);
    $this->get(route('third-party-risk.dashboard.get-vendors-data', ['data_scope' => $data_scope]))
        ->assertOk()
        ->assertJsonCount(2)
        ->assertJsonFragment([
            'level' => 1,
            'color' => "#ff0000",
            'name' => "Level 1",
        ])
        ->assertJsonPath('projects_progress.In Progress', 1);
});

it('scopes project options when creating a project', function () {
    $admin = loginWithRole('Third Party Risk Administrator');
    $data_scope = getScope($admin);

    $questionnaire = Questionnaire::factory()->has(Question::factory())->create();
    setScope($questionnaire, $data_scope);

    $vendor = Vendor::factory()->create();
    setScope($vendor, $data_scope);

    $this
        ->get(route('third-party-risk.projects.options', ['data_scope' => $data_scope]))
        ->assertOk()
        ->assertJsonCount(1, 'vendors')
        ->assertJsonCount(1, 'questionnaires');
});

it('can only see the top vendors from it\'s own department ', function () {
    $admin = loginWithRole('Third Party Risk Administrator');
    $data_scope = getScope($admin);

    $vendors = collect();
    for ($i = 0; $i < 5; $i++) {
        $project = Project::factory()->in_progress()->create();
        setScope($project, $data_scope);
        $vendor = $project->vendor;
        setScope($vendor, $data_scope);
        $vendors->push($vendor);
    }

    $this->get(route('third-party-risk.dashboard.get-top-vendors', ['data_scope' => $data_scope]))
        ->assertOk()
        ->assertJsonPath('data.total', $vendors->count());
});
