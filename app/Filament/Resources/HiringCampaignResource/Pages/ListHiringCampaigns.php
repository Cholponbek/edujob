<?php

namespace App\Filament\Resources\HiringCampaignResource\Pages;

use App\Filament\Resources\HiringCampaignResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHiringCampaigns extends ListRecords
{
    protected static string $resource = HiringCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
