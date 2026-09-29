<?php

namespace App\Policies;

use App\Models\Institution;
use App\Models\StaffRequest;
use App\Models\User;

class StaffRequestPolicy
{
    /**
     * platform_admin (Filament) видит и правит всё — институционные
     * ограничения ниже касаются только сотрудников учреждений.
     */
    public function before(User $user, string $ability): ?bool
    {
        return $user->is_platform_admin ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->institutionRole() !== null;
    }

    public function view(User $user, StaffRequest $staffRequest): bool
    {
        return $this->belongsToSameInstitution($user, $staffRequest);
    }

    /**
     * Создать вакансию может любой сотрудник учреждения (director/HR/staff)
     * — черновик, публикация уже отдельная способность (см. publish()).
     * Вызывается как authorize('create', [StaffRequest::class, $institution]).
     */
    public function create(User $user, Institution $institution): bool
    {
        return $user->institutions()->where('institutions.id', $institution->id)->exists();
    }

    public function update(User $user, StaffRequest $staffRequest): bool
    {
        return $this->belongsToSameInstitution($user, $staffRequest)
            && $staffRequest->status !== 'closed';
    }

    /**
     * Принятое решение (ARCHITECTURE.md): director/HR/staff с разными
     * правами — публикация доступна только director/HR, staff готовит
     * черновик, но не публикует его сам.
     */
    public function publish(User $user, StaffRequest $staffRequest): bool
    {
        return $this->belongsToSameInstitution($user, $staffRequest)
            && in_array($user->institutionRole(), ['director', 'hr'], true);
    }

    public function delete(User $user, StaffRequest $staffRequest): bool
    {
        return $this->belongsToSameInstitution($user, $staffRequest)
            && $staffRequest->status === 'draft';
    }

    private function belongsToSameInstitution(User $user, StaffRequest $staffRequest): bool
    {
        return $user->institutions()->where('institutions.id', $staffRequest->institution_id)->exists();
    }
}
