<?php

namespace Fuascailtdev\FilamentResourceBuilder\Tests\Feature;

use Fuascailtdev\FilamentResourceBuilder\Models\DynamicResource;
use Fuascailtdev\FilamentResourceBuilder\Services\DynamicResourceRegistrationService;
use Fuascailtdev\FilamentResourceBuilder\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DynamicResourceRegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Run our migrations
        $this->artisan('migrate', ['--database' => 'testing']);
    }

    /** @test */
    public function it_can_generate_resource_classes_for_dynamic_resources()
    {
        // Create some dynamic resources
        $products = DynamicResource::create(['name' => 'Products']);
        $products->fields()->create([
            'name' => 'title',
            'label' => 'Product Title',
            'type' => 'text',
            'required' => true,
        ]);

        $members = DynamicResource::create(['name' => 'Members']);
        $members->fields()->create([
            'name' => 'name',
            'label' => 'Member Name',
            'type' => 'text',
            'required' => true,
        ]);

        // Generate resource classes
        $service = new DynamicResourceRegistrationService;
        $resourceClasses = $service->generateResourceClasses();

        // Should have generated 2 classes
        $this->assertCount(2, $resourceClasses);

        // Each should be a valid class name
        foreach ($resourceClasses as $className) {
            $this->assertTrue(class_exists($className), "Class {$className} should exist");
        }

        echo "\n✅ Generated " . count($resourceClasses) . " dynamic resource classes!\n";

        foreach ($resourceClasses as $className) {
            echo "  - {$className}\n";
        }
    }

    /** @test */
    public function it_only_generates_classes_for_active_resources()
    {
        // Create active resource
        $activeResource = DynamicResource::create([
            'name' => 'Active Resource',
            'is_active' => true,
        ]);
        $activeResource->fields()->create([
            'name' => 'title',
            'label' => 'Title',
            'type' => 'text',
        ]);

        // Create inactive resource
        $inactiveResource = DynamicResource::create([
            'name' => 'Inactive Resource',
            'is_active' => false,
        ]);
        $inactiveResource->fields()->create([
            'name' => 'title',
            'label' => 'Title',
            'type' => 'text',
        ]);

        // Generate resource classes
        $service = new DynamicResourceRegistrationService;
        $resourceClasses = $service->generateResourceClasses();

        // Should only have 1 class (the active one)
        $this->assertCount(1, $resourceClasses);

        echo "\n✅ Only generated classes for active resources!\n";
    }

    /** @test */
    public function it_handles_empty_dynamic_resources_gracefully()
    {
        // No dynamic resources exist

        $service = new DynamicResourceRegistrationService;
        $resourceClasses = $service->generateResourceClasses();

        // Should return empty array
        $this->assertCount(0, $resourceClasses);
        $this->assertIsArray($resourceClasses);

        echo "\n✅ Handles empty resources gracefully!\n";
    }
}
