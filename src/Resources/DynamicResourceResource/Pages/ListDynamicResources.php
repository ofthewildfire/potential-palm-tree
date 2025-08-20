<?php

namespace Fuascailtdev\FilamentResourceBuilder\Resources\DynamicResourceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Fuascailtdev\FilamentResourceBuilder\Resources\DynamicResourceResource;

class ListDynamicResources extends ListRecords
{
    protected static string $resource = DynamicResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}