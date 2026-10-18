<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('password'); // admin, technician, user
            $table->string('department')->nullable()->after('role');
            $table->string('phone')->nullable()->after('department');
            $table->string('specialty')->nullable()->after('phone'); // สำหรับช่าง เช่น 'ฮาร์ดแวร์ / คอมพิวเตอร์'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'department', 'phone', 'specialty']);
        });
    }
};
