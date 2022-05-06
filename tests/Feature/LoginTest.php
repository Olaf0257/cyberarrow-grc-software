<?php

use App\Models\UserManagement\Admin;
use App\Utils\RegularFunctions;
use Inertia\Testing\Assert;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

it('has login page', function () {
    get(route('login'))
        ->assertOk()
        ->assertInertia(function (Assert $page) {
            return $page->component('auth/LoginPage');
        });
});

it('can login if status is active', function () {
    $user = Admin::factory()->create();

    $credentials = [
      'email' => $user->email,
      'password' => 'password',
      'status' => $user->status
    ];

    post(route('login'),$credentials)->assertRedirect(RegularFunctions::getRoleBasedRedirectPath());
});

it('cannot login if status is disabled or unverified', function () {
    $status = ['unverified', 'disabled'];

    $user = Admin::factory()->create([
        'status' => $status[array_rand($status)]
    ]);

    $credentials = [
        'email' => $user->email,
        'password' => 'password',
        'status' => $user->status
    ];

    $msg = $user->status === 'unverified' ? 'Email not verified' : 'User with this email has been disabled';

    post(route('login'),$credentials)
        ->assertRedirect(route('homepage'))
        ->assertInvalid(['email' => $msg]);
});