<?php

use App\Models\Compliance\Standard;
use Illuminate\Http\UploadedFile;
use Inertia\Testing\Assert;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\post;
use function Pest\Laravel\get;
use function Pest\Laravel\seed;

beforeEach(function (){
   $this->followingRedirects();
});

it('can create a control with csv file upload', function ($file){
    loginWithRole();

    $standard = Standard::factory()->create();

    $data['csv_upload'] = $file;

    post(route('compliance-template-upload-csv-store-controls',['standard' => $standard->id]),$data)
        ->assertInertia(function (Assert $page){
            $page->component('compliance-template/ControlList')
                ->where('flash.success', 'Controls Uploaded Successfully!');
        });

    assertDatabaseCount('compliance_standard_controls', 4);
})->with([
    function(){
        return new UploadedFile(base_path('tests/Feature/Administration/files/control.csv'), 'control.csv', 'text/csv', null, true);
    },
    function(){
        return new UploadedFile(base_path('tests/Feature/Administration/files/control-with-zero.csv'), 'control-with-zero.csv', 'text/csv', null, true);
    },
    function(){
        return new UploadedFile(base_path('tests/Feature/Administration/files/control-with-zero.csv'), 'control-with-zero.csv', 'text/csv', null, true);
    }
]);

it('shouldn\'t allow to upload invalid csv file', function ($file){
    loginWithRole();

    $standard = Standard::factory()->create();

    $data['csv_upload'] = $file;

    $this->from(route('compliance-template-create-controls',$standard->id))->post(route('compliance-template-upload-csv-store-controls',['standard' => $standard->id]),$data)
        ->assertInertia(function (Assert $page){
            return $page->component('compliance-template/ControlCreate')
                ->has('flash.csv_upload_error');
        });
})->with([
    function(){
        return new UploadedFile(base_path('tests/Feature/Administration/files/bad-control-2.csv'), 'bad-control-2.csv', 'text/csv', null, true);
    },
    function(){
        return new UploadedFile(base_path('tests/Feature/Administration/files/bad-control-2.csv'), 'bad-control-2.csv', 'text/csv', null, true);
    },
    function(){
        return new UploadedFile(base_path('tests/Feature/Administration/files/bad-control-3.csv'), 'bad-control-3.csv', 'text/csv', null, true);
    },
    function(){
        return new UploadedFile(base_path('tests/Feature/Administration/files/bad-control-4.csv'), 'bad-control-4.csv', 'text/csv', null, true);
    }
]);

it('search should work as it\'s supposed to', function () {
    loginWithRole();

    seed(\Database\Seeders\Compliance\DefaultComplianceStandardsSeeder::class);

    $keyword = 'C';

    get(route('compliance-template-get-json-data', ['data-scope' => '1-0', 'page' => '1', 'per_page', '10', 'search' => $keyword]))
    ->assertOk()
    ->assertJsonPath('data.total', 29);

});