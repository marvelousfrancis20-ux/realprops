<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('property_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('description');
            $table->decimal('price', 15, 2);
            $table->string('currency', 3)->default('NGN');
            $table->string('listing_type', 20)->default('sale');
            $table->string('status', 20)->default('available');

            $table->unsignedTinyInteger('bedrooms')->default(0);
            $table->unsignedTinyInteger('bathrooms')->default(0);
            $table->unsignedTinyInteger('toilets')->default(0);
            $table->unsignedInteger('square_meters')->nullable();
            $table->unsignedSmallInteger('year_built')->nullable();

            $table->boolean('is_featured')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->string('rejected_reason')->nullable();
            $table->string('featured_until')->nullable();

            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('country')->default('Nigeria');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->string('video_url')->nullable();
            $table->string('virtual_tour_url')->nullable();

            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('og_image')->nullable();

            $table->unsignedInteger('views_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Composite / filter indexes (properties listing is the hot path)
            $table->index(['status', 'is_approved'], 'properties_status_approved_index');
            $table->index(['listing_type', 'status'], 'properties_listing_type_status_index');
            $table->index(['property_type_id', 'status'], 'properties_type_status_index');
            $table->index('city', 'properties_city_index');
            $table->index('state', 'properties_state_index');
            $table->index('price', 'properties_price_index');
            $table->index('bedrooms', 'properties_bedrooms_index');
            $table->index('agent_id', 'properties_agent_index');
            $table->index('is_featured', 'properties_featured_index');
            $table->index('published_at', 'properties_published_at_index');
            $table->index('created_at', 'properties_created_at_index');
            $table->index(['latitude', 'longitude'], 'properties_coordinates_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
