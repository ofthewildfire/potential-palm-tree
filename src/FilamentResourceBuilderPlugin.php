<?php

namespace Fuascailtdev\FilamentResourceBuilder;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentResourceBuilderPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-resource-builder';
    }

    public function register(Panel $panel): void
    {
        // Only register the resource if the database table exists
        try {
            if (\Schema::hasTable('dynamic_resources')) {
                $panel->resources([
                    \Fuascailtdev\FilamentResourceBuilder\Resources\DynamicResourceResource::class,
                ]);
            }
        } catch (\Exception $e) {
            // Database not available, skip registration
            \Log::debug('FilamentResourceBuilder: Could not check for dynamic_resources table: ' . $e->getMessage());
        }
    }

    public function boot(Panel $panel): void
    {
        // Temporarily disabled to debug 500 error
        // $this->registerDynamicResources($panel);
    }

    /**
     * Register dynamic resources with the panel
     */
    protected function registerDynamicResources(Panel $panel): void
    {
        try {
            // Only try to register if database is available and tables exist
            if (! app()->runningInConsole() || app()->runningUnitTests()) {
                // Check if the required tables exist before trying to query them
                if (\Schema::hasTable('dynamic_resources')) {
                    $registrationService = app(\Fuascailtdev\FilamentResourceBuilder\Services\DynamicResourceRegistrationService::class);

                    $resourceClasses = $registrationService->generateResourceClasses();

                    if (! empty($resourceClasses)) {
                        $panel->resources($resourceClasses);
                    }
                }
            }
        } catch (\Exception $e) {
            // Silently fail if database is not available (during migrations, etc.)
            // In production, you might want to log this
            \Log::debug('FilamentResourceBuilder: Could not register dynamic resources: ' . $e->getMessage());
        }
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
