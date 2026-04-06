<?php

namespace App\Filament\Resources\DiscountCodes\Pages;

use App\Filament\Resources\DiscountCodes\DiscountCodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDiscountCodes extends ManageRecords
{
    protected static string $resource = DiscountCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
