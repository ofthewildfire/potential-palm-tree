<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Table to store dynamic resource definitions
        Schema::create('dynamic_resources', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Members"
            $table->string('slug')->unique(); // e.g., "members"
            $table->string('table_name')->unique(); // e.g., "members"
            $table->string('model_name'); // e.g., "Member"
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Table to store field definitions for each resource
        Schema::create('dynamic_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dynamic_resource_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "first_name"
            $table->string('label'); // e.g., "First Name"
            $table->string('type'); // e.g., "text", "number", "select"
            $table->json('options')->nullable(); // Store field-specific options
            $table->boolean('required')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dynamic_fields');
        Schema::dropIfExists('dynamic_resources');
    }
};
