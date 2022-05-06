<?php

use App\Models\Administration\OrganizationManagement\Organization;
use App\Models\Compliance\Project;
use Inertia\Testing\Assert;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\delete;
use function Pest\Laravel\from;
use function Pest\Laravel\get;

beforeEach(function (){
    $this->followingRedirects();
});

it('cannot access projects page when unauthenticated', function () {
    get(route('compliance-projects-view'))
        ->assertInertia(function (Assert $page) {
        return $page->component('auth/LoginPage');
    });
});

it('can access project list page only by Global Admin, Compliance Administrator and Contributor role', function ($role){
    loginWithRole($role);

    if(in_array($role, ['Global Admin', 'Compliance Administrator', 'Contributor'])) {
        get(route('compliance-projects-view'))
            ->assertOk()
            ->assertInertia(function (Assert $page){
                return $page->component('compliance/project-list-page/ProjectListPage');
            });
    }else{
        get(route('compliance-projects-view'))->assertForbidden();
    }
})->with('roles');

it('can access project create page only by Global Admin and Compliance Administrator role', function ($role){
    loginWithRole($role);

    if(in_array($role, ['Global Admin', 'Compliance Administrator'])) {
        get(route('compliance-projects-create'))
            ->assertOk()
            ->assertInertia(function (Assert $page){
                return $page->component('compliance/project-create-page/ProjectCreatePage');
            });
    }else{
        get(route('compliance-projects-create'))->assertForbidden();
    }
})->with('roles');

it('can access project detail page only by Global Admin, Compliance Administrator role', function ($role){
    loginWithRole($role);

    $project = Project::factory()->create();

    if(in_array($role, ['Global Admin', 'Compliance Administrator'])) {
        get(route('compliance-project-show',$project->id))
            ->assertOk()
            ->assertInertia(function (Assert $page){
                return $page->component('compliance/project-details/ProjectDetails');
            });
    }elseif($role === 'Contributor'){
        ajaxGet(route('compliance-project-show',$project->id))->assertJson([
            'success' => false,
            'message' => 'Access Denied!!',
        ]);
    }
    else{
        get(route('compliance-project-show',$project->id))->assertForbidden();
    }
})->with('roles');

it('can delete project only by Global Admin and Compliance Administrator role', function ($role){
    $admin = loginWithRole($role);
    $data_scope = getScope($admin);

    $project = Project::factory()->create();
    setScope($project,$data_scope);

    if(in_array($role, ['Global Admin', 'Compliance Administrator'])) {
        $this->from(route('compliance-projects-view'))
            ->delete(route('compliance-projects-delete', ['project' => $project->id, 'data_scope' => $data_scope]))
            ->assertInertia(function (Assert $page){
                return $page->component('compliance/project-list-page/ProjectListPage')
                    ->where('flash.success', 'Project deleted successfully.');
            });

        assertDatabaseCount('compliance_projects', 0);
    }else{
        delete(route('compliance-projects-delete', ['project' => $project->id, 'data_scope' => $data_scope]))->assertForbidden();
    }
})->with('roles');