<?php

namespace Fuascailtdev\FilamentResourceBuilder\Tests\Feature;

use Fuascailtdev\FilamentResourceBuilder\Models\DynamicResource;
use Fuascailtdev\FilamentResourceBuilder\Resources\GenericDynamicResource;
use Fuascailtdev\FilamentResourceBuilder\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenericDynamicResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Run our migrations
        $this->artisan('migrate', ['--database' => 'testing']);
    }

    /** @test */
    public function it_can_configure_for_a_dynamic_resource()
    {
        // Create a dynamic resource
        $dynamicResource = DynamicResource::create([
            'name' => 'Products',
        ]);

        $dynamicResource->fields()->create([
            'name' => 'title',
            'label' => 'Product Title',
            'type' => 'text',
            'required' => true,
        ]);

        $dynamicResource->fields()->create([
            'name' => 'price',
            'label' => 'Price',
            'type' => 'number',
            'required' => true,
        ]);

        // Configure the generic resource
        GenericDynamicResource::configureFor($dynamicResource);

        // Test that it has the right navigation label
        $this->assertEquals('Products', GenericDynamicResource::getNavigationLabel());

        echo "\n✅ Generic resource configured for: " . GenericDynamicResource::getNavigationLabel() . "\n";
    }

    /** @test */
    public function it_can_switch_between_different_resources()
    {
        // Create first resource
        $products = DynamicResource::create(['name' => 'Products']);
        $products->fields()->create([
            'name' => 'title',
            'label' => 'Product Title',
            'type' => 'text',
            'required' => true,
        ]);

        // Create second resource
        $members = DynamicResource::create(['name' => 'Members']);
        $members->fields()->create([
            'name' => 'name',
            'label' => 'Member Name',
            'type' => 'text',
            'required' => true,
        ]);

        // Configure for products
        GenericDynamicResource::configureFor($products);
        $this->assertEquals('Products', GenericDynamicResource::getNavigationLabel());

        // Switch to members
        GenericDynamicResource::configureFor($members);
        $this->assertEquals('Members', GenericDynamicResource::getNavigationLabel());

        echo "\n✅ Successfully switched between different dynamic resources!\n";
    }
}
