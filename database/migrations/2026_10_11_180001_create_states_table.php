<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('code', 16)->nullable();
            $table->string('country')->default('Nigeria');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['country', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
