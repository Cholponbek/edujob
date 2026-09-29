<?php

namespace App\Http\Controllers\Institution;

use App\Http\Controllers\Controller;
use App\Models\Institution;
use App\Models\StaffRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StaffRequestController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $institution = $this->currentInstitution($request);
        $this->authorize('viewAny', StaffRequest::class);

        return response()->json(
            $institution->staffRequests()->latest()->get()
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $institution = $this->currentInstitution($request);
        $this->authorize('create', [StaffRequest::class, $institution]);

        $data = $this->validated($request);

        $institution->staffRequests()->create([
            ...$data,
            'created_by' => $request->user()->id,
            'status' => 'draft',
        ]);

        return back()->with('status', 'staff-request-created');
    }

    public function update(Request $request, StaffRequest $staffRequest): RedirectResponse
    {
        $this->authorize('update', $staffRequest);

        $staffRequest->update($this->validated($request));

        return back()->with('status', 'staff-request-updated');
    }

    /**
     * Публикация — единственное место, где реально проверяется активная
     * подписка учреждения (ARCHITECTURE.md, чеклист "Биллинг" — до этого
     * hasActiveSubscription() существовал, но нигде не проверялся).
     */
    public function publish(Request $request, StaffRequest $staffRequest): RedirectResponse
    {
        $this->authorize('publish', $staffRequest);

        abort_unless(
            $staffRequest->institution->hasActiveSubscription(),
            402,
            'Публикация вакансий доступна только при активной подписке учреждения.'
        );

        $staffRequest->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return back()->with('status', 'staff-request-published');
    }

    public function close(Request $request, StaffRequest $staffRequest): RedirectResponse
    {
        $this->authorize('update', $staffRequest);

        $staffRequest->update(['status' => 'closed']);

        return back()->with('status', 'staff-request-closed');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'education_level' => ['required', 'in:preschool,primary,secondary,vocational,higher'],
            'required_category' => ['nullable', 'string', 'max:255'],
            'employment_type' => ['required', 'in:full_time,part_time,either'],
            'stake_fraction' => ['required', 'numeric', 'min:0.1', 'max:1'],
            'is_next_school_year' => ['boolean'],
            'description' => ['nullable', 'string'],
            'salary_from' => ['nullable', 'integer', 'min:0'],
            'salary_to' => ['nullable', 'integer', 'gte:salary_from'],
        ]);
    }

    private function currentInstitution(Request $request): Institution
    {
        $institution = $request->user()->institutions()->first();

        abort_unless($institution !== null, 403, 'Только сотрудник учреждения может управлять вакансиями.');

        return $institution;
    }
}
