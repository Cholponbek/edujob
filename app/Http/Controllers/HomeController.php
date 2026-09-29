<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\StaffRequest;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $featuredVacancies = StaffRequest::query()
            ->with('institution')
            ->where('status', 'published')
            ->latest('published_at')
            ->limit(24)
            ->get(['id', 'institution_id', 'title', 'subject', 'employment_type', 'salary_from', 'salary_to']);

        return Inertia::render('Welcome', [
            'stats' => [
                'vacancies' => StaffRequest::where('status', 'published')->count(),
                'institutions' => Institution::where('verification_status', 'verified')->count(),
                'regions' => Institution::whereHas('staffRequests', fn ($q) => $q->where('status', 'published'))
                    ->distinct('region')->count('region'),
            ],
            'featuredVacancies' => $featuredVacancies,
        ]);
    }
}
