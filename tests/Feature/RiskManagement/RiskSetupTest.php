<?php

use function Pest\Laravel\get;

it('can download sample', function () {
    loginWithRole();

    get(route('risks.manual.download-sample'))->assertOk();
});
