<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 32)->nullable()->after('email');
            $table->string('whatsapp', 32)->nullable()->after('phone');
            $table->text('bio')->nullable()->after('whatsapp');
            $table->string('avatar_path')->nullable()->after('bio');
            $table->boolean('is_suspended')->default(false)->after('password');
            $table->timestamp('suspended_at')->nullable()->after('is_suspended');
            $table->string('suspension_reason')->nullable()->after('suspended_at');

            $table->index('is_suspended');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'whatsapp', 'bio', 'avatar_path',
                'is_suspended', 'suspended_at', 'suspension_reason',
            ]);
        });
    }
};
