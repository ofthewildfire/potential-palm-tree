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
        $panel->resources([
            \Fuascailtdev\FilamentResourceBuilder\Resources\DynamicResourceResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // Register dynamic resources
        $this->registerDynamicResources($panel);
    }

    /**
     * Register dynamic resources with the panel
     */
    protected function registerDynamicResources(Panel $panel): void
    {
        try {
            // Only try to register if database is available
            if (!app()->runningInConsole() || app()->runningUnitTests()) {
                $registrationService = app(\Fuascailtdev\FilamentResourceBuilder\Services\DynamicResourceRegistrationService::class);
                
                $resourceClasses = $registrationService->generateResourceClasses();
                
                if (!empty($resourceClasses)) {
                    $panel->resources($resourceClasses);
                }
            }
        } catch (\Exception $e) {
            // Silently fail if database is not available (during migrations, etc.)
            // In production, you might want to log this
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
