<?php

namespace Fuascailtdev\FilamentResourceBuilder\Services;

use Fuascailtdev\FilamentResourceBuilder\Models\DynamicResource;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DynamicTableService
{
    /**
     * Create a database table for a dynamic resource
     */
    public function createTable(DynamicResource $resource): bool
    {
        $tableName = $resource->table_name;

        // Don't create if table already exists
        if (Schema::hasTable($tableName)) {
            return false;
        }

        Schema::create($tableName, function (Blueprint $table) use ($resource) {
            $table->id();

            // Add fields based on the resource configuration
            foreach ($resource->fields as $field) {
                $this->addFieldToTable($table, $field);
            }

            $table->timestamps();
        });

        return true;
    }

    /**
     * Drop a database table for a dynamic resource
     */
    public function dropTable(DynamicResource $resource): bool
    {
        $tableName = $resource->table_name;

        if (Schema::hasTable($tableName)) {
            Schema::dropIfExists($tableName);

            return true;
        }

        return false;
    }

    /**
     * Update a table when fields change
     */
    public function updateTable(DynamicResource $resource): bool
    {
        $tableName = $resource->table_name;

        if (! Schema::hasTable($tableName)) {
            return $this->createTable($resource);
        }

        // For now, we'll recreate the table
        // In production, you'd want more sophisticated migration logic
        $this->dropTable($resource);

        return $this->createTable($resource);
    }

    /**
     * Add a field to the table blueprint
     */
    protected function addFieldToTable(Blueprint $table, $field): void
    {
        $column = match ($field->type) {
            'text' => $table->string($field->name),
            'textarea' => $table->text($field->name),
            'number' => $table->integer($field->name),
            'email' => $table->string($field->name),
            'password' => $table->string($field->name),
            'select' => $table->string($field->name),
            'checkbox' => $table->boolean($field->name)->default(false),
            'date' => $table->date($field->name),
            'datetime' => $table->dateTime($field->name),
            default => $table->string($field->name),
        };

        // Make nullable if not required
        if (! $field->required) {
            $column->nullable();
        }
    }

    /**
     * Check if a table exists for a resource
     */
    public function tableExists(DynamicResource $resource): bool
    {
        return Schema::hasTable($resource->table_name);
    }
}
