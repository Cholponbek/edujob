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
        return Inertia::render('Welcome', [
            'stats' => [
                'vacancies' => StaffRequest::where('status', 'published')->count(),
                'institutions' => Institution::where('verification_status', 'verified')->count(),
            ],
        ]);
    }
}
