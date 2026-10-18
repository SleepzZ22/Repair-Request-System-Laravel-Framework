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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_no')->unique(); // เช่น TK-202610-001
            $table->string('title');
            $table->string('category');
            $table->string('asset_code')->nullable();
            $table->string('location');
            $table->string('priority')->default('normal'); // normal, high, urgent
            $table->text('description');
            $table->json('images')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reporter_name');
            $table->string('department');
            $table->string('phone');
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('technician_name')->nullable();
            $table->string('status')->default('pending'); // pending, in_progress, waiting_parts, completed, cancelled
            $table->text('repair_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('ticket_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('by_name');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_logs');
        Schema::dropIfExists('tickets');
    }
};
