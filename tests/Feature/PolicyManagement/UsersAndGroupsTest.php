<?php

use App\Models\Compliance\Project;
use App\Models\PolicyManagement\Group\Group;
use Inertia\Testing\Assert;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\followingRedirects;
use function Pest\Laravel\from;
use function Pest\Laravel\post;

it('can update groups without changing name', function () {
    $admin = loginWithRole();
    $data_scope = getScope($admin);

    Project::factory()->create();

    $group = Group::factory()->make();
    $group['users']['groupsData'] = [
        0 => [
            'user_first_name' => "Test",
            'user_last_name' => "User",
            'user_email' => "test@gmail.com"
        ]
    ];

    post(route('policy-management.users-and-groups.groups.store',['data_scope' => $data_scope]), $group->toArray())->assertSessionHas('success', 'Group added successfully');
    assertDatabaseCount('policy_groups', 1);

    $group = Group::first();
    $group['users']['groupsData'] = [
        0 => [
            'user_first_name' => "Test",
            'user_last_name' => "User",
            'user_email' => "test@gmail.com"
        ]
    ];

    post(route('policy-management.users-and-groups.groups.update',['id' => $group->id,'data_scope' => $data_scope]), $group->toArray())->assertSessionHas('success', 'Group updated successfully.');
});
