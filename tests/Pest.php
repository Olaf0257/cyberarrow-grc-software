<?php

use App\Models\Administration\OrganizationManagement\Department;
use App\Models\Administration\OrganizationManagement\Organization;
use App\Models\DataScope\Scopable;
use App\Models\UserManagement\Admin;
use App\Models\UserManagement\AdminDepartment;
use Database\Seeders\Admin\DefaultOrganization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use \App\Models\ThirdPartyRisk\Question;
use \App\Models\ThirdPartyRisk\Questionnaire;
use Tests\TestCase;
use function Pest\Laravel\seed;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/



uses(TestCase::class, RefreshDatabase::class, \Illuminate\Foundation\Testing\WithFaker::class)->in(__DIR__);

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/


/**
 * Set the currently logged in user for the application.
 */
function actingAs($user, string $driver = 'admin'): TestCase
{
    return test()->actingAs($user, $driver);
}

function loginWithRole($role = 'Global Admin')
{
    if ($role === 'Global Admin') {
        $user = Admin::first();
    } else {
        $user = Admin::factory()->create();
        $user->assignRole($role);
    }

    assignDepartment($user);
    actingAs($user);

    return $user;
}

function ajaxGet($route)
{
    return test()->withHeaders(['HTTP_X-Requested-With' => 'XMLHttpRequest'])
        ->get($route);
}

function newDepartmentAdmin($role = "Global Admin")
{
    // create data for new department
    $second_department_admin = Admin::factory()->create();
    $second_department_admin->assignRole($role);

    assignDepartment($second_department_admin);

    return $second_department_admin;
}

/**
 * @param $admin
 * @param int $parent_id
 * @return mixed
 *
 * for creation of a child department pass a second argument, for the parent id
 */
function assignDepartment($admin, $parent_id = 0)
{
    seed([
        DefaultOrganization::class,
    ]);

    // new department
    $organization = Organization::first();
    $department = Department::factory()
        ->state([
            'organization_id' => $organization->id,
            'parent_id' => $parent_id,
        ])
        ->create();

    // assign department
    $department = new AdminDepartment([
        'admin_id' => $admin->id,
        'organization_id' => $organization->id,
        'department_id' => $department->id
    ]);

    return $admin->department()->save($department);
}

function getScope($admin)
{
    $department = $admin->department;
    $departmentId = $department->department_id ?: 0;
    return $department->organization_id. '-'. $departmentId;
}

/**
 * @param $model
 * @param string $scope
 * @return mixed
 *  Scope should be dynamically get from the user.
 */
function setScope($model, $scope)
{
    $dataScope = explode('-', $scope);
    $organizationId = $dataScope[0];
    $departmentId = $dataScope[1];

    return Scopable::create([
        'organization_id' => $organizationId,
        'department_id'=> $departmentId ?: null,
        'scopable_id'=>$model->id,
        'scopable_type'=>get_class($model)
    ]);
}

/**
 * @param  array $models
 * @return array
 * Pass in an array the modes for which data should be created.
 */
function createBaseData($models)
{
    // establish the base admin and scope
    $base_admin = newDepartmentAdmin();
    actingAs($base_admin);
    $base_data_scope = getScope($base_admin);

    $data = [];
    foreach ($models as $model_type) {
        $model = app($model_type);
        $model = $model->factory()->create();
        if($model instanceof Questionnaire){
            //create a question for it
            $model->questions()->create(Question::factory()->make()->toArray());
        }
        setScope($model, $base_data_scope);
        $data[$model_type] = [
            'data_scope' => $base_data_scope,
            'collection' => $model,
        ];
    }

    return $data;
}

