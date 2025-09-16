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
        Schema::create('offline_payment_notifications', function (Blueprint $table) {
        $table->id();
        $table->foreignId('monthly_offline_cost_id')
            ->constrained('monthly_offline_costs')
            ->onDelete('cascade');
        $table->enum('level', ['green','yellow','red']);
        $table->enum('status', ['active','cleared'])->default('active'); // clearer naming
        $table->integer('days_left')->nullable();
        $table->timestamp('generated_at')->nullable();
        $table->timestamp('updated_level_at')->nullable();
        $table->timestamp('cleared_at')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_payment_notifications');
    }
};
