<?php

namespace Fuascailtdev\FilamentResourceBuilder\Resources\GenericDynamicResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Fuascailtdev\FilamentResourceBuilder\Resources\GenericDynamicResource;

class EditGenericDynamicResource extends EditRecord
{
    protected static string $resource = GenericDynamicResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}