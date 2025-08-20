<?php

namespace Fuascailtdev\FilamentResourceBuilder\Services;

use Fuascailtdev\FilamentResourceBuilder\Models\DynamicResource;
use Illuminate\Database\Eloquent\Model;

class DynamicModelService
{
    /**
     * Create a dynamic Eloquent model class
     */
    public function createModel(DynamicResource $resource): string
    {
        $className = $resource->model_name;
        $tableName = $resource->table_name;

        // Get fillable fields
        $fillable = $resource->fields->pluck('name')->toArray();

        // Get cast types
        $casts = $this->getCasts($resource);

        // Create the model class dynamically
        $modelClass = new class extends Model
        {
            // These will be set dynamically
        };

        // Set the table name
        $modelClass->setTable($tableName);

        // Set fillable fields
        $modelClass->fillable($fillable);

        // Set casts
        foreach ($casts as $field => $cast) {
            $modelClass->addCast($field, $cast);
        }

        return get_class($modelClass);
    }

    /**
     * Get cast types for fields
     */
    protected function getCasts(DynamicResource $resource): array
    {
        $casts = [];

        foreach ($resource->fields as $field) {
            $cast = match ($field->type) {
                'number' => 'integer',
                'checkbox' => 'boolean',
                'date' => 'date',
                'datetime' => 'datetime',
                default => null,
            };

            if ($cast) {
                $casts[$field->name] = $cast;
            }
        }

        return $casts;
    }

    /**
     * Get validation rules for a resource
     */
    public function getValidationRules(DynamicResource $resource): array
    {
        $rules = [];

        foreach ($resource->fields as $field) {
            $fieldRules = [];

            if ($field->required) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            // Add type-specific rules
            switch ($field->type) {
                case 'email':
                    $fieldRules[] = 'email';

                    break;
                case 'number':
                    $fieldRules[] = 'integer';

                    break;
                case 'date':
                    $fieldRules[] = 'date';

                    break;
                case 'datetime':
                    $fieldRules[] = 'date';

                    break;
            }

            if (! empty($fieldRules)) {
                $rules[$field->name] = $fieldRules;
            }
        }

        return $rules;
    }
}
