<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('property_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->string('path')->nullable();
            $table->string('url')->nullable(); // remote/CDN image (seeded or externally hosted)
            $table->string('alt')->nullable();
            $table->string('caption')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_cover')->default(false);
            $table->timestamps();

            $table->index(['property_id', 'sort_order']);
            $table->index('is_cover');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('property_images');
    }
};
