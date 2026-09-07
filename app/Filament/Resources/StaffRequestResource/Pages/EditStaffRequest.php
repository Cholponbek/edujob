<?php

namespace App\Filament\Resources\StaffRequestResource\Pages;

use App\Filament\Resources\StaffRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStaffRequest extends EditRecord
{
    protected static string $resource = StaffRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
