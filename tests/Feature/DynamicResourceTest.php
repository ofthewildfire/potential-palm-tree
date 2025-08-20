<?php

namespace Fuascailtdev\FilamentResourceBuilder\Tests\Feature;

use Fuascailtdev\FilamentResourceBuilder\Models\DynamicResource;
use Fuascailtdev\FilamentResourceBuilder\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DynamicResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Run our migrations
        $this->artisan('migrate', ['--database' => 'testing']);
    }

    /** @test */
    public function it_can_create_a_dynamic_resource()
    {
        $resource = DynamicResource::create([
            'name' => 'Members',
            'description' => 'A resource for managing members',
        ]);

        $this->assertEquals('Members', $resource->name);
        $this->assertEquals('members', $resource->slug);
        $this->assertEquals('members', $resource->table_name);
        $this->assertEquals('Member', $resource->model_name);
        $this->assertTrue($resource->is_active);
    }

    /** @test */
    public function it_can_add_fields_to_a_resource()
    {
        $resource = DynamicResource::create([
            'name' => 'Products',
        ]);

        $resource->fields()->create([
            'name' => 'title',
            'label' => 'Product Title',
            'type' => 'text',
            'required' => true,
            'sort_order' => 1,
        ]);

        $resource->fields()->create([
            'name' => 'price',
            'label' => 'Price',
            'type' => 'number',
            'required' => true,
            'sort_order' => 2,
        ]);

        $this->assertCount(2, $resource->fields);
        $this->assertEquals('title', $resource->fields->first()->name);
        $this->assertEquals('Product Title', $resource->fields->first()->label);
    }
}
