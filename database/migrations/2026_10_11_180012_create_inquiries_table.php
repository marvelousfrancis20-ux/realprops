<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete();

            $table->string('name');
            $table->string('email');
            $table->string('phone', 32)->nullable();
            $table->text('message');
            $table->date('preferred_tour_date')->nullable();
            $table->string('status', 20)->default('new');
            $table->text('reply_message')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();

            $table->index(['property_id', 'status']);
            $table->index(['agent_id', 'status']);
            $table->index('user_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
