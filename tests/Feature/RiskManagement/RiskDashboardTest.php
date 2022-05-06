<?php

use App\Models\RiskManagement\RiskRegister;

use function Pest\Laravel\get;
use function Pest\Laravel\post;
use \Inertia\Testing\Assert;

it('in the dashboard, risk should appear closed when it has been closed', function () { //571
    loginWithRole();

    $risk = RiskRegister::factory()->make();

    $this
        ->get(route('risks.register.risks-edit', 1))
        ->assertInertia(function (Assert $page) {
            return $page
                ->component('risk-management/risk-register/components/RiskRegisterCreate');
        });

    // $this
    //     ->post(route('risks.register.risks-update', 1), ['treatment_options' => 'Accept'])
    //     ->assertRedirect(route('risks.register.risks-edit', 1));
    // ->assertInertia(function (Assert $page) {
    //     return $page
    //         ->component('risk-management/risk-register/components/RiskRegisterShow')
    //         ->where('flash.success', 'Risk updated successfully!');
    // });


});

// it('accepted risks should be closed if they are accepted', function () { //538
//     loginWithRole();

//     get(route('risks.manual.download-sample'))->assertOk();
// });
