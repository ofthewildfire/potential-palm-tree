<?php

namespace Fuascailtdev\FilamentResourceBuilder\Resources\GenericDynamicResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Fuascailtdev\FilamentResourceBuilder\Resources\GenericDynamicResource;

class ListGenericDynamicResources extends ListRecords
{
    protected static string $resource = GenericDynamicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}