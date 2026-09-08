<?php

namespace App\Http\Controllers;

use App\Jobs\Ai\GenerateBilingualResumeJob;
use App\Jobs\Ai\ParseResumeDocumentJob;
use App\Jobs\Ai\RecommendStaffRequestsJob;
use App\Models\Candidate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CandidateResumeController extends Controller
{
    public function uploadResume(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'resume' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $candidate = Candidate::firstOrCreate(['user_id' => $request->user()->id]);

        $path = $data['resume']->store('resumes', 'public');
        $candidate->update(['resume_source_path' => $path]);

        ParseResumeDocumentJob::dispatch($candidate->id);

        return back()->with('status', 'resume-uploaded');
    }

    public function generateResume(Request $request): RedirectResponse
    {
        $candidate = Candidate::firstOrCreate(['user_id' => $request->user()->id]);

        GenerateBilingualResumeJob::dispatch($candidate->id);

        return back()->with('status', 'resume-generation-queued');
    }

    public function refreshRecommendations(Request $request): RedirectResponse
    {
        $candidate = Candidate::firstOrCreate(['user_id' => $request->user()->id]);

        RecommendStaffRequestsJob::dispatch($candidate->id);

        return back()->with('status', 'recommendations-queued');
    }
}
