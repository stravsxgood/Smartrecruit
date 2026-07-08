<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobPositionRequest;
use App\Http\Requests\UpdateJobPositionRequest;
use App\Models\JobPosition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JobPositionController extends Controller
{
    public function index(): Response
    {
        $jobPositions = JobPosition::with('creator')
            ->latest()
            ->get();

        return Inertia::render('JobPositions/Index', [
            'jobPositions' => $jobPositions,
        ]);
    }

    public function store(StoreJobPositionRequest $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $user->jobPositions()->create($request->validated());

        return redirect()
            ->back()
            ->with('success', 'Job position created successfully.');
    }

    public function show(JobPosition $jobPosition): Response
    {
        $jobPosition->load('creator');

        return Inertia::render('JobPositions/Show', [
            'jobPosition' => $jobPosition,
        ]);
    }

    public function showAnalysis(Request $request): Response
    {
        return Inertia::render('Show', [
            'fileName' => $request->query('fileName'),
            'analysisResult' => 'Analisis CV sukses dilakukan oleh AI.',
        ]);
    }

    public function update(UpdateJobPositionRequest $request, JobPosition $jobPosition): RedirectResponse
    {
        $jobPosition->update($request->validated());

        return redirect()
            ->back()
            ->with('success', 'Job position updated successfully.');
    }

    public function destroy(Request $request, JobPosition $jobPosition): RedirectResponse
    {
        $user = $request->user();

        if (! $user || $user->role !== 'hrd') {
            abort(403);
        }

        $jobPosition->delete();

        return redirect()
            ->route('job-positions.index')
            ->with('success', 'Job position deleted successfully.');
    }
}
