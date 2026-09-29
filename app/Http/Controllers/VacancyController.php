<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\StaffRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VacancyController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only(['subject', 'region', 'education_level', 'employment_type']);

        $staffRequests = StaffRequest::query()
            ->with('institution')
            ->where('status', 'published')
            ->when($filters['subject'] ?? null, fn ($q, $v) => $q->where('subject', $v))
            ->when($filters['education_level'] ?? null, fn ($q, $v) => $q->where('education_level', $v))
            ->when($filters['employment_type'] ?? null, fn ($q, $v) => $q->where('employment_type', $v))
            ->when($filters['region'] ?? null, fn ($q, $v) => $q->whereHas(
                'institution',
                fn ($iq) => $iq->where('region', $v)
            ))
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Vacancies/Index', [
            'staffRequests' => $staffRequests,
            'filters' => $filters,
            'filterOptions' => [
                'subjects' => StaffRequest::where('status', 'published')->whereNotNull('subject')->distinct()->orderBy('subject')->pluck('subject'),
                'regions' => Institution::whereHas('staffRequests', fn ($q) => $q->where('status', 'published'))->distinct()->orderBy('region')->pluck('region'),
            ],
        ]);
    }

    public function show(StaffRequest $staffRequest): Response
    {
        abort_unless($staffRequest->status === 'published', 404);

        $staffRequest->load('institution');

        $hasApplied = false;
        $user = request()->user();

        if ($user?->candidate) {
            $hasApplied = $staffRequest->applications()
                ->where('candidate_id', $user->candidate->id)
                ->exists();
        }

        return Inertia::render('Vacancies/Show', [
            'staffRequest' => $staffRequest,
            'hasApplied' => $hasApplied,
        ]);
    }
}
