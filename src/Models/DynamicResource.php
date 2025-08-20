<?php

namespace Fuascailtdev\FilamentResourceBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DynamicResource extends Model
{
    protected $fillable = [
        'name',
        'slug', 
        'table_name',
        'model_name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all fields for this resource
     */
    public function fields(): HasMany
    {
        return $this->hasMany(DynamicField::class)->orderBy('sort_order');
    }

    /**
     * Generate the slug from the name
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = str($value)->slug();
        $this->attributes['table_name'] = str($value)->plural()->snake();
        $this->attributes['model_name'] = str($value)->studly()->singular();
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (!isset($model->attributes['is_active'])) {
                $model->attributes['is_active'] = true;
            }
        });

        static::saved(function ($model) {
            // Create or update the database table when resource is saved
            if ($model->is_active && $model->fields()->count() > 0) {
                app(\Fuascailtdev\FilamentResourceBuilder\Services\DynamicTableService::class)
                    ->updateTable($model);
            }
        });

        static::deleting(function ($model) {
            // Drop the table when resource is deleted
            app(\Fuascailtdev\FilamentResourceBuilder\Services\DynamicTableService::class)
                ->dropTable($model);
        });
    }
}