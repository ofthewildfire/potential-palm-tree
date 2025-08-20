<?php

namespace Fuascailtdev\FilamentResourceBuilder\Resources\DynamicResourceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Fuascailtdev\FilamentResourceBuilder\Resources\DynamicResourceResource;

class EditDynamicResource extends EditRecord
{
    protected static string $resource = DynamicResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}