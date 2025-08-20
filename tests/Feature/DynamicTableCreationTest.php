<?php

namespace Fuascailtdev\FilamentResourceBuilder\Tests\Feature;

use Fuascailtdev\FilamentResourceBuilder\Models\DynamicResource;
use Fuascailtdev\FilamentResourceBuilder\Services\DynamicTableService;
use Fuascailtdev\FilamentResourceBuilder\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

class DynamicTableCreationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run our migrations
        $this->artisan('migrate', ['--database' => 'testing']);
    }

    /** @test */
    public function it_creates_a_database_table_when_resource_is_saved()
    {
        // Create a resource with fields
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

        $resource->fields()->create([
            'name' => 'description',
            'label' => 'Description',
            'type' => 'textarea',
            'required' => false,
            'sort_order' => 3,
        ]);

        // Refresh to trigger the saved event
        $resource->refresh();
        $resource->save();

        // Check if the table was created
        $this->assertTrue(Schema::hasTable('products'));
        
        // Check if the table has the correct columns
        $this->assertTrue(Schema::hasColumn('products', 'id'));
        $this->assertTrue(Schema::hasColumn('products', 'title'));
        $this->assertTrue(Schema::hasColumn('products', 'price'));
        $this->assertTrue(Schema::hasColumn('products', 'description'));
        $this->assertTrue(Schema::hasColumn('products', 'created_at'));
        $this->assertTrue(Schema::hasColumn('products', 'updated_at'));
    }

    /** @test */
    public function it_can_manually_create_and_drop_tables()
    {
        $resource = DynamicResource::create([
            'name' => 'Events',
        ]);

        $resource->fields()->create([
            'name' => 'title',
            'label' => 'Event Title',
            'type' => 'text',
            'required' => true,
        ]);

        $service = new DynamicTableService();
        
        // Create table
        $created = $service->createTable($resource);
        $this->assertTrue($created);
        $this->assertTrue(Schema::hasTable('events'));

        // Drop table
        $dropped = $service->dropTable($resource);
        $this->assertTrue($dropped);
        $this->assertFalse(Schema::hasTable('events'));
    }

    /** @test */
    public function it_handles_different_field_types()
    {
        $resource = DynamicResource::create([
            'name' => 'Members',
        ]);

        // Add various field types
        $fieldTypes = [
            ['name' => 'name', 'type' => 'text'],
            ['name' => 'email', 'type' => 'email'],
            ['name' => 'age', 'type' => 'number'],
            ['name' => 'bio', 'type' => 'textarea'],
            ['name' => 'is_active', 'type' => 'checkbox'],
            ['name' => 'birth_date', 'type' => 'date'],
            ['name' => 'last_login', 'type' => 'datetime'],
        ];

        foreach ($fieldTypes as $index => $fieldType) {
            $resource->fields()->create([
                'name' => $fieldType['name'],
                'label' => ucfirst(str_replace('_', ' ', $fieldType['name'])),
                'type' => $fieldType['type'],
                'required' => false,
                'sort_order' => $index + 1,
            ]);
        }

        $service = new DynamicTableService();
        $service->createTable($resource);

        // Check all columns exist
        foreach ($fieldTypes as $fieldType) {
            $this->assertTrue(
                Schema::hasColumn('members', $fieldType['name']),
                "Column {$fieldType['name']} should exist"
            );
        }
    }
}