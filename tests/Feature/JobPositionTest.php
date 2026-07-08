<?php

use App\Models\JobPosition;
use App\Models\User;

it('allows hrd to create job position', function () {
    $hrd = User::factory()->create(['role' => 'hrd']);

    $response = $this->actingAs($hrd)->post('/job-positions', [
        'title' => 'Software Engineer',
        'description' => 'Great job',
        'status' => 'open',
        'requirements' => ['PHP', 'Vue'],
        'benefits' => ['Remote'],
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('job_positions', [
        'title' => 'Software Engineer',
        'status' => 'open',
    ]);
});

it('prevents candidate from creating job position', function () {
    $candidate = User::factory()->create(['role' => 'candidate']);

    $response = $this->actingAs($candidate)->post('/job-positions', [
        'title' => 'Software Engineer',
        'description' => 'Great job',
        'status' => 'open',
    ]);

    $response->assertForbidden();
});
