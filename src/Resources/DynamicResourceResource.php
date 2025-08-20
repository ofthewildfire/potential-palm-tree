<?php

namespace Fuascailtdev\FilamentResourceBuilder\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Fuascailtdev\FilamentResourceBuilder\Models\DynamicField;
use Fuascailtdev\FilamentResourceBuilder\Models\DynamicResource;
use Fuascailtdev\FilamentResourceBuilder\Resources\DynamicResourceResource\Pages;

class DynamicResourceResource extends Resource
{
    protected static ?string $model = DynamicResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Resource Builder';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Resource Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                if ($state) {
                                    $set('slug', str($state)->slug());
                                    $set('table_name', str($state)->plural()->snake());
                                    $set('model_name', str($state)->studly()->singular());
                                }
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('table_name')
                            ->required()
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('model_name')
                            ->required(),

                        Forms\Components\Textarea::make('description')
                            ->rows(3),

                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Fields')
                    ->schema([
                        Forms\Components\Repeater::make('fields')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                                        if ($state) {
                                            $set('label', str($state)->title());
                                        }
                                    }),

                                Forms\Components\TextInput::make('label')
                                    ->required(),

                                Forms\Components\Select::make('type')
                                    ->required()
                                    ->options(DynamicField::getFieldTypes())
                                    ->live(),

                                Forms\Components\Toggle::make('required')
                                    ->default(false),

                                Forms\Components\KeyValue::make('options')
                                    ->visible(fn (Forms\Get $get) => in_array($get('type'), ['select']))
                                    ->keyLabel('Option Value')
                                    ->valueLabel('Option Label'),
                            ])
                            ->columns(2)
                            ->reorderable('sort_order')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['label'] ?? $state['name'] ?? null),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('table_name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('fields_count')
                    ->counts('fields')
                    ->label('Fields'),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDynamicResources::route('/'),
            'create' => Pages\CreateDynamicResource::route('/create'),
            'edit' => Pages\EditDynamicResource::route('/{record}/edit'),
        ];
    }
}
