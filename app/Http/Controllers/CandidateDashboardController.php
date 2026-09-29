<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CandidateDashboardController extends Controller
{
    private const PROFILE_FIELDS = ['subject', 'teaching_category', 'education_levels', 'bio', 'diploma_document_path'];

    public function index(Request $request): Response
    {
        $user = $request->user();

        // Сотрудники учреждений уже управляют вакансиями через
        // Institution\StaffRequestController — свой кабинет для них не
        // построен (см. ARCHITECTURE.md), пока заглушка.
        if ($user->institutionRole() !== null) {
            return Inertia::render('Dashboard', ['isInstitutionUser' => true]);
        }

        $candidate = $user->candidate;

        $completedFields = $candidate
            ? collect(self::PROFILE_FIELDS)->filter(fn ($field) => filled($candidate->$field))->count()
            : 0;

        return Inertia::render('Candidate/Dashboard', [
            'candidate' => $candidate,
            'profileCompletion' => (int) round($completedFields / count(self::PROFILE_FIELDS) * 100),
            'applications' => $candidate
                ? $candidate->applications()->with('staffRequest.institution')->latest()->get()
                : [],
            'recommendations' => $candidate
                ? $candidate->recommendations()->with('staffRequest.institution')->orderByDesc('score')->limit(5)->get()
                : [],
        ]);
    }
}
