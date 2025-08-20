<?php

namespace Fuascailtdev\FilamentResourceBuilder\Tests\Feature;

use Filament\Panel;
use Fuascailtdev\FilamentResourceBuilder\FilamentResourceBuilderPlugin;
use Fuascailtdev\FilamentResourceBuilder\Resources\DynamicResourceResource;
use Fuascailtdev\FilamentResourceBuilder\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FilamentResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Run our migrations
        $this->artisan('migrate', ['--database' => 'testing']);
    }

    /** @test */
    public function it_can_register_the_plugin()
    {
        $panel = Panel::make()
            ->id('admin')
            ->plugin(FilamentResourceBuilderPlugin::make());

        $this->assertInstanceOf(Panel::class, $panel);
    }

    /** @test */
    public function it_registers_the_dynamic_resource_resource()
    {
        $resources = [DynamicResourceResource::class];

        $this->assertContains(DynamicResourceResource::class, $resources);
        $this->assertEquals('Resource Builder', DynamicResourceResource::getNavigationLabel());
    }
}
