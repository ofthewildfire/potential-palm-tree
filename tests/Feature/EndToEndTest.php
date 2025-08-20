<?php

namespace Fuascailtdev\FilamentResourceBuilder\Tests\Feature;

use Fuascailtdev\FilamentResourceBuilder\Models\DynamicResource;
use Fuascailtdev\FilamentResourceBuilder\Services\DynamicFilamentResourceService;
use Fuascailtdev\FilamentResourceBuilder\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EndToEndTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run our migrations
        $this->artisan('migrate', ['--database' => 'testing']);
    }

    /** @test */
    public function it_demonstrates_the_complete_workflow()
    {
        // 1. User creates a "Products" resource through the GUI
        $resource = DynamicResource::create([
            'name' => 'Products',
            'description' => 'Manage our product catalog',
        ]);

        // 2. User adds fields using the repeater
        $resource->fields()->create([
            'name' => 'title',
            'label' => 'Product Title',
            'type' => 'text',
            'required' => true,
            'sort_order' => 1,
        ]);

        $resource->fields()->create([
            'name' => 'price',
            'label' => 'Price ($)',
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

        $resource->fields()->create([
            'name' => 'is_featured',
            'label' => 'Featured Product',
            'type' => 'checkbox',
            'required' => false,
            'sort_order' => 4,
        ]);

        // 3. Save triggers table creation
        $resource->save();

        // 4. Verify the table exists with correct structure
        $this->assertTrue(Schema::hasTable('products'));
        $this->assertTrue(Schema::hasColumn('products', 'title'));
        $this->assertTrue(Schema::hasColumn('products', 'price'));
        $this->assertTrue(Schema::hasColumn('products', 'description'));
        $this->assertTrue(Schema::hasColumn('products', 'is_featured'));

        // 5. Test that we can actually insert data into the new table
        DB::table('products')->insert([
            'title' => 'Awesome Widget',
            'price' => 29,
            'description' => 'The best widget you can buy!',
            'is_featured' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 6. Verify the data was inserted
        $product = DB::table('products')->first();
        $this->assertEquals('Awesome Widget', $product->title);
        $this->assertEquals(29, $product->price);
        $this->assertTrue((bool) $product->is_featured);

        // 7. Test Filament resource generation
        $filamentService = new DynamicFilamentResourceService();
        $formFields = $filamentService->createFormFields($resource);
        $tableColumns = $filamentService->createTableColumns($resource);

        // Verify we got the right number of form fields and table columns
        $this->assertCount(4, $formFields);
        $this->assertCount(5, $tableColumns); // 4 fields + created_at

        echo "\n🎉 SUCCESS! Your dynamic resource system works!\n";
        echo "✅ Created 'products' table with 4 custom fields\n";
        echo "✅ Inserted test data successfully\n";
        echo "✅ Generated " . count($formFields) . " form fields\n";
        echo "✅ Generated " . count($tableColumns) . " table columns\n";
    }
}