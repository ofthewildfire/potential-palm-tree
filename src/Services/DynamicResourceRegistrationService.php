<?php

namespace Fuascailtdev\FilamentResourceBuilder\Services;

use Fuascailtdev\FilamentResourceBuilder\Models\DynamicResource;
use Fuascailtdev\FilamentResourceBuilder\Resources\GenericDynamicResource;

class DynamicResourceRegistrationService
{
    /**
     * Generate resource classes for all active dynamic resources
     */
    public function generateResourceClasses(): array
    {
        $resourceClasses = [];

        // Get all active dynamic resources
        $dynamicResources = DynamicResource::where('is_active', true)
            ->with('fields')
            ->get();

        foreach ($dynamicResources as $dynamicResource) {
            $resourceClass = $this->createResourceClass($dynamicResource);
            $resourceClasses[] = $resourceClass;
        }

        return $resourceClasses;
    }

    /**
     * Create a resource class for a specific dynamic resource
     */
    protected function createResourceClass(DynamicResource $dynamicResource): string
    {
        // Create a unique class name
        $className = $dynamicResource->model_name . 'DynamicResource';

        // Create the class dynamically
        $resourceClass = new class($dynamicResource) extends GenericDynamicResource
        {
            protected DynamicResource $dynamicResourceConfig;

            public function __construct(DynamicResource $dynamicResource)
            {
                $this->dynamicResourceConfig = $dynamicResource;

                // Configure the parent class
                self::configureFor($dynamicResource);
            }

            public static function getNavigationLabel(): string
            {
                // This will be set when the class is created
                return 'Dynamic Resource';
            }

            public static function getNavigationIcon(): ?string
            {
                return 'heroicon-o-rectangle-stack';
            }
        };

        // Store the class reference with a unique identifier
        $this->storeResourceClass($className, $resourceClass, $dynamicResource);

        return get_class($resourceClass);
    }

    /**
     * Store the resource class for later use
     */
    protected function storeResourceClass(string $className, object $resourceClass, DynamicResource $dynamicResource): void
    {
        // We'll store these in a registry for later access
        app()->singleton("dynamic_resource_{$dynamicResource->slug}", function () use ($resourceClass) {
            return $resourceClass;
        });
    }

    /**
     * Get all registered dynamic resource classes
     */
    public function getRegisteredClasses(): array
    {
        $classes = [];

        $dynamicResources = DynamicResource::where('is_active', true)->get();

        foreach ($dynamicResources as $dynamicResource) {
            if (app()->bound("dynamic_resource_{$dynamicResource->slug}")) {
                $resourceInstance = app("dynamic_resource_{$dynamicResource->slug}");
                $classes[] = get_class($resourceInstance);
            }
        }

        return $classes;
    }
}
