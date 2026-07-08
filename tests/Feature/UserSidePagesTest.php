<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('guest cannot access user side pages', function () {
    $this->get('/dashboard')->assertRedirect('/login');
    $this->get('/my-profile')->assertRedirect('/login');
    $this->get('/settings/profile')->assertRedirect('/login');
    $this->get('/settings/security')->assertRedirect('/login');
    $this->get('/settings/appearance')->assertRedirect('/login');
});

test('authenticated user can access dashboard page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
        );
});

test('authenticated user can access candidate file upload page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/my-profile')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('ApplicantProfiles/Show')
        );
});

test('authenticated user can access profile settings page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings/profile')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/Profile')
        );
});

test('authenticated user can access appearance settings page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/settings/appearance')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('settings/Appearance')
        );
});
