<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('agency_name')->nullable();
            $table->string('license_number')->nullable();
            $table->string('id_document_path')->nullable();
            $table->string('license_document_path')->nullable();
            $table->unsignedInteger('years_experience')->default(0);
            $table->text('bio')->nullable();
            $table->string('whatsapp', 32)->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->string('verified_by')->nullable();
            $table->timestamps();

            $table->unique('user_id');
            $table->index('verified_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_profiles');
    }
};
