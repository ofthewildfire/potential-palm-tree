<?php

namespace Fuascailtdev\FilamentResourceBuilder\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DynamicField extends Model
{
    protected $fillable = [
        'dynamic_resource_id',
        'name',
        'label',
        'type',
        'options',
        'required',
        'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
        'required' => 'boolean',
    ];

    /**
     * Get the resource this field belongs to
     */
    public function dynamicResource(): BelongsTo
    {
        return $this->belongsTo(DynamicResource::class);
    }

    /**
     * Available field types
     */
    public static function getFieldTypes(): array
    {
        return [
            'text' => 'Text Input',
            'textarea' => 'Textarea',
            'number' => 'Number',
            'email' => 'Email',
            'password' => 'Password',
            'select' => 'Select Dropdown',
            'checkbox' => 'Checkbox',
            'date' => 'Date',
            'datetime' => 'Date & Time',
        ];
    }
}
