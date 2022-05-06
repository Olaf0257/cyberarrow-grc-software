<?php

use App\Models\RiskManagement\RiskRegister;
use Database\Seeders\RiskManagement\RiskCategoriesSeeder;
use Database\Seeders\RiskManagement\RiskMatrix\ImpactDefaultSeeder;
use Database\Seeders\RiskManagement\RiskMatrix\LikelihoodDefaultSeeder;
use Database\Seeders\RiskManagement\RiskMatrix\ScoreDefaultSeeder;
use Illuminate\Http\UploadedFile;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\seed;
use function PHPUnit\Framework\assertEquals;

beforeEach(function () {
    loginWithRole('Risk Administrator');

    seed(ScoreDefaultSeeder::class);
});

it('can register risks as Risk Administrator', function () {
    $riskData = RiskRegister::factory()->make();

    $this->from(route('risks.register.risks-create'))->post(route('risks.register.risks-store'), $riskData->toArray())
        ->assertRedirect(route('risks.register.index'))
        ->assertSessionHas('success', 'Risk added successfully!');

    assertDatabaseCount('risks_register', 1);
})->only();


it('displays Inherent Risk Score and the Residual Risk Score correctly when set 2X2', function () {
    $riskData = RiskRegister::factory()->make([
        'likelihood' => 1,
        'impact' => 1
    ]);

    post(route('risks.register.risks-store'), $riskData->toArray())->assertSessionHas('success', 'Risk added successfully!');

    assertDatabaseCount('risks_register', 1);

    $risk = RiskRegister::first();

    assertEquals(4, $risk->inherent_score, 'Inherent score matched.');
    assertEquals(4, $risk->residual_score, 'Residual score matched.');
});

it('can update risk without changing name', function () {
    $riskData = RiskRegister::factory()->make();

    post(route('risks.register.risks-store'), $riskData->toArray())->assertSessionHas('success', 'Risk added successfully!');

    assertDatabaseCount('risks_register', 1);

    $risk = RiskRegister::first();

    $updateRisk = [
        'name' => $risk->name,
        'affected_properties' => $riskData->affected_properties,
        'treatment_options' => $riskData->treatment_options,
        'likelihood' => $riskData->likelihood,
        'impact' => $riskData->impact
    ];

    post(route('risks.register.risks-update', $risk->id), $updateRisk)->assertSessionHas('success', 'Risk updated successfully!');

});

it('can register risk by selecting all affected properties', function () {
    $riskData = RiskRegister::factory()->make([
        'affected_properties' => ['Confidentiality', 'Integrity', 'Availability', 'Change Management', 'Ethical', 'Financial', 'Financial Reporting', 'Fraud', 'Geographic', 'IT Operations', 'Logical Access', 'Material Misstatement', 'Operational', 'Privacy', 'Regulatory / Compliance', 'Reputational', 'Strategy']
    ]);

    post(route('risks.register.risks-store'), $riskData->toArray())->assertSessionHas('success', 'Risk added successfully!');

    assertDatabaseCount('risks_register', 1);

    //check if risks-register has only 1 data
    get(route('risks.register.registered-risks', ['id' => $riskData->category]))
        ->assertJsonCount(1, 'data');
});

it('displays likelihood and impact correctly when csv uploaded', function () {
    seed([
        RiskCategoriesSeeder::class,
        LikelihoodDefaultSeeder::class,
        ImpactDefaultSeeder::class
    ]);

    $file['csv_upload'] = new UploadedFile(base_path('tests/Feature/RiskManagement/files/risk-setup-test.csv'), 'risk-setup-test.csv', 'text/csv', null, true);

    post(route('risks.manual.risks-import'), $file)
        ->assertSessionHas('success', 'All rows successfully inserted');

    assertDatabaseCount('risks_register', 1);

    $risk = RiskRegister::first();

    assertDatabaseHas('risks_register', [
        'name' => $risk->name,
        'risk_description' => $risk->risk_description,
        'affected_properties' => $risk->affected_properties,
        'affected_functions_or_assets' => $risk->affected_functions_or_assets,
        'treatment' => $risk->treatment,
        'category_id' => $risk->category_id,
        'treatment_options' => $risk->treatment_options,
        'likelihood' => 3,
        'impact' => 3
    ]);
});

it('can upload csv with multiple rows', function () {
    seed([
        RiskCategoriesSeeder::class,
        LikelihoodDefaultSeeder::class,
        ImpactDefaultSeeder::class
    ]);

    $file['csv_upload'] = new UploadedFile(base_path('tests/Feature/RiskManagement/files/risk-setup-multiple-rows.csv'), 'risk-setup-multiple-rows.csv', 'text/csv', null, true);

    post(route('risks.manual.risks-import'), $file)
        ->assertSessionHas('success', 'All rows successfully inserted');

    assertDatabaseCount('risks_register', 32);
});

it('trims the spaces for affected properties on csv manual import', function () {
    seed([
        RiskCategoriesSeeder::class,
        LikelihoodDefaultSeeder::class,
        ImpactDefaultSeeder::class
    ]);

    $data['csv_upload'] = new UploadedFile(base_path('tests/Feature/RiskManagement/files/risk-with-space-in-affected-properties.csv'), 'risk-with-space-in-affected-properties-1.csv', 'text/csv', null, true);

    post(route('risks.manual.risks-import'), $data)
        ->assertSessionHas('success', 'All rows successfully inserted');

    assertDatabaseHas('risks_register', [
        'name' => 'Risk Name',
        'affected_properties' => 'Change Management'
    ]);

    assertDatabaseHas('risks_register', [
        'name' => 'Risk Name 1',
        'affected_properties' => 'Change Management'
    ]);
});

it('gets top risks in dashboard with index column', function () {
    $riskData = RiskRegister::factory()->make();

    post(route('risks.register.risks-store'), $riskData->toArray())->assertSessionHas('success', 'Risk added successfully!');

    get(route('risks.dashboard.get-dasboard-data-datatable'))
        ->assertJsonCount(1,'data.data')
        ->assertJsonPath('data.data.0.index', 1);

    assertDatabaseCount('risks_register', 1);
});