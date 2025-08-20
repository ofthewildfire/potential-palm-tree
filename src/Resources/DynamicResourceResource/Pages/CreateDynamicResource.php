<?php

namespace Fuascailtdev\FilamentResourceBuilder\Resources\DynamicResourceResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Fuascailtdev\FilamentResourceBuilder\Resources\DynamicResourceResource;

class CreateDynamicResource extends CreateRecord
{
    protected static string $resource = DynamicResourceResource::class;
}