<?php

use App\Models\ApplicantProfile;
use App\Models\User;

it('allows user to create applicant profile', function () {
    $user = User::factory()->create(['role' => 'candidate']);

    $response = $this->actingAs($user)->post('/my-profile', [
        'skills' => ['PHP', 'Vue'],
        'experience' => ['1 year at Tech Co'],
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('applicant_profiles', [
        'user_id' => $user->id,
    ]);
});

it('allows user to update their profile', function () {
    $user = User::factory()->create(['role' => 'candidate']);
    $profile = ApplicantProfile::create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->put('/my-profile/' . $profile->id, [
        'skills' => ['PHP', 'Vue', 'Laravel'],
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('applicant_profiles', [
        'id' => $profile->id,
    ]);
});
