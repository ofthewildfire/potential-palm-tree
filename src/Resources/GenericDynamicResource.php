<?php

namespace Fuascailtdev\FilamentResourceBuilder\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Fuascailtdev\FilamentResourceBuilder\Models\DynamicResource;
use Illuminate\Database\Eloquent\Model;

class GenericDynamicResource extends Resource
{
    protected static ?string $model = null; // We'll set this dynamically

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    // This will hold our dynamic resource configuration
    protected static ?DynamicResource $dynamicResource = null;

    /**
     * Set up this resource for a specific dynamic resource
     */
    public static function configureFor(DynamicResource $dynamicResource): void
    {
        static::$dynamicResource = $dynamicResource;
        static::$model = static::createDynamicModel($dynamicResource);
    }

    /**
     * Get the navigation label
     */
    public static function getNavigationLabel(): string
    {
        return static::$dynamicResource?->name ?? 'Dynamic Resource';
    }

    /**
     * Create a dynamic model class for this resource
     */
    protected static function createDynamicModel(DynamicResource $dynamicResource): string
    {
        // Create an anonymous model class
        $modelClass = new class extends Model
        {
            protected $guarded = [];
        };

        // Set the table name
        $modelClass->setTable($dynamicResource->table_name);

        // Set fillable fields
        $fillable = $dynamicResource->fields->pluck('name')->toArray();
        $modelClass->fillable($fillable);

        return get_class($modelClass);
    }

    public static function form(Form $form): Form
    {
        if (! static::$dynamicResource) {
            return $form->schema([]);
        }

        $fields = [];

        // Generate form fields based on the dynamic resource configuration
        foreach (static::$dynamicResource->fields as $field) {
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

                'checkbox' => Forms\Components\Checkbox::make($field->name)
                    ->label($field->label),

                'date' => Forms\Components\DatePicker::make($field->name)
                    ->label($field->label)
                    ->required($field->required),

                'datetime' => Forms\Components\DateTimePicker::make($field->name)
                    ->label($field->label)
                    ->required($field->required),

                'select' => Forms\Components\Select::make($field->name)
                    ->label($field->label)
                    ->options($field->options ?? [])
                    ->required($field->required),

                default => Forms\Components\TextInput::make($field->name)
                    ->label($field->label)
                    ->required($field->required),
            };

            $fields[] = $formField;
        }

        return $form->schema($fields);
    }

    public static function table(Table $table): Table
    {
        if (! static::$dynamicResource) {
            return $table->columns([]);
        }

        $columns = [];

        // Generate table columns based on the dynamic resource configuration
        foreach (static::$dynamicResource->fields as $field) {
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

        // Add timestamps
        $columns[] = Tables\Columns\TextColumn::make('created_at')
            ->dateTime()
            ->sortable();

        return $table
            ->columns($columns)
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

    public static function getPages(): array
    {
        return [
            'index' => \Fuascailtdev\FilamentResourceBuilder\Resources\GenericDynamicResource\Pages\ListGenericDynamicResources::route('/'),
            'create' => \Fuascailtdev\FilamentResourceBuilder\Resources\GenericDynamicResource\Pages\CreateGenericDynamicResource::route('/create'),
            'edit' => \Fuascailtdev\FilamentResourceBuilder\Resources\GenericDynamicResource\Pages\EditGenericDynamicResource::route('/{record}/edit'),
        ];
    }
}
