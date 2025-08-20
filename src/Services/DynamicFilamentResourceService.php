<?php

namespace Fuascailtdev\FilamentResourceBuilder\Services;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Fuascailtdev\FilamentResourceBuilder\Models\DynamicResource;
use Illuminate\Database\Eloquent\Model;

class DynamicFilamentResourceService
{
    /**
     * Create a Filament resource class for a dynamic resource
     */
    public function createFilamentResource(DynamicResource $dynamicResource): string
    {
        $resourceName = $dynamicResource->model_name . 'Resource';
        
        // Create a dynamic model first
        $modelClass = $this->createDynamicModel($dynamicResource);

        // Create the Filament resource class
        $resourceClass = new class($modelClass, $dynamicResource) extends Resource {
            protected static ?string $model = null;
            protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
            protected DynamicResource $dynamicResource;

            public function __construct($modelClass, DynamicResource $dynamicResource)
            {
                static::$model = $modelClass;
                $this->dynamicResource = $dynamicResource;
            }

            public static function form(Form $form): Form
            {
                return $form->schema(static::getFormSchema());
            }

            public static function table(Table $table): Table
            {
                return $table
                    ->columns(static::getTableColumns())
                    ->filters([])
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

            protected static function getFormSchema(): array
            {
                // This will be populated with dynamic fields
                return [];
            }

            protected static function getTableColumns(): array
            {
                // This will be populated with dynamic columns
                return [];
            }

            public static function getNavigationLabel(): string
            {
                return 'Dynamic Resources'; // Will be set dynamically
            }
        };

        return get_class($resourceClass);
    }

    /**
     * Create form fields for a dynamic resource
     */
    public function createFormFields(DynamicResource $resource): array
    {
        $fields = [];

        foreach ($resource->fields as $field) {
            $formField = match ($field->type) {
                'text' => Forms\Components\TextInput::make($field->name)
                    ->label($field->label)
                    ->required($field->required),
                
                'textarea' => Forms\Components\Textarea::make($field->name)
                    ->label($field->label)
                    ->required($field->required),
                
                'number' => Forms\Components\TextInput::make($field->name)
                    ->label($field->label)
                    ->numeric()
                    ->required($field->required),
                
                'email' => Forms\Components\TextInput::make($field->name)
                    ->label($field->label)
                    ->email()
                    ->required($field->required),
                
                'password' => Forms\Components\TextInput::make($field->name)
                    ->label($field->label)
                    ->password()
                    ->required($field->required),
                
                'select' => Forms\Components\Select::make($field->name)
                    ->label($field->label)
                    ->options($field->options ?? [])
                    ->required($field->required),
                
                'checkbox' => Forms\Components\Checkbox::make($field->name)
                    ->label($field->label),
                
                'date' => Forms\Components\DatePicker::make($field->name)
                    ->label($field->label)
                    ->required($field->required),
                
                'datetime' => Forms\Components\DateTimePicker::make($field->name)
                    ->label($field->label)
                    ->required($field->required),
                
                default => Forms\Components\TextInput::make($field->name)
                    ->label($field->label)
                    ->required($field->required),
            };

            $fields[] = $formField;
        }

        return $fields;
    }

    /**
     * Create table columns for a dynamic resource
     */
    public function createTableColumns(DynamicResource $resource): array
    {
        $columns = [];

        foreach ($resource->fields as $field) {
            $column = match ($field->type) {
                'checkbox' => Tables\Columns\IconColumn::make($field->name)
                    ->label($field->label)
                    ->boolean(),
                
                'date', 'datetime' => Tables\Columns\TextColumn::make($field->name)
                    ->label($field->label)
                    ->date(),
                
                default => Tables\Columns\TextColumn::make($field->name)
                    ->label($field->label)
                    ->searchable()
                    ->sortable(),
            };

            $columns[] = $column;
        }

        // Always add timestamps
        $columns[] = Tables\Columns\TextColumn::make('created_at')
            ->dateTime()
            ->sortable();

        return $columns;
    }

    /**
     * Create a dynamic model class
     */
    protected function createDynamicModel(DynamicResource $resource): string
    {
        $fillable = $resource->fields->pluck('name')->toArray();
        $tableName = $resource->table_name;

        $modelClass = new class extends Model {
            protected $guarded = [];
        };

        $modelClass->setTable($tableName);
        $modelClass->fillable($fillable);

        return get_class($modelClass);
    }
}