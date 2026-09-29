<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\StaffRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ApplicationController extends Controller
{
    public function index(Request $request, StaffRequest $staffRequest): JsonResponse
    {
        $this->authorize('view', $staffRequest);

        return response()->json(
            $staffRequest->applications()->with('candidate.user')->get()
        );
    }

    public function updateStatus(Request $request, Application $application): RedirectResponse
    {
        $this->authorize('view', $application->staffRequest);

        $data = $request->validate([
            'status' => ['required', Rule::in(['hired', 'rejected'])],
        ]);

        $application->update($data);

        return back()->with('status', 'application-status-updated');
    }
}
