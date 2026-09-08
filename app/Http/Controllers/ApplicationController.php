<?php

namespace App\Http\Controllers;

use App\Jobs\Ai\GenerateScreeningQuestionsJob;
use App\Jobs\Ai\ScoreScreeningApplicationJob;
use App\Models\Application;
use App\Models\Candidate;
use App\Models\StaffRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'staff_request_id' => ['required', 'exists:staff_requests,id'],
        ]);

        $staffRequest = StaffRequest::findOrFail($data['staff_request_id']);
        $candidate = Candidate::firstOrCreate(['user_id' => $request->user()->id]);

        $application = Application::firstOrCreate([
            'staff_request_id' => $staffRequest->id,
            'candidate_id' => $candidate->id,
        ]);

        // ИИ-скрининг всегда асинхронно — никогда синхронно в запросе
        // (ARCHITECTURE.md §3, принцип #2).
        GenerateScreeningQuestionsJob::dispatch($application->id);

        return back()->with('status', 'application-created');
    }

    public function submitAnswers(Request $request, Application $application): RedirectResponse
    {
        abort_unless($application->candidate->user_id === $request->user()->id, 403);

        $data = $request->validate([
            'answers' => ['required', 'array'],
            'answers.*' => ['required', 'string'],
        ]);

        $application->update(['screening_answers' => $data['answers']]);

        ScoreScreeningApplicationJob::dispatch($application->id);

        return back()->with('status', 'answers-submitted');
    }
}
