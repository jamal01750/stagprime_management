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
    
        Schema::create('monthly_targets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('year');
            $table->unsignedInteger('month');
            $table->decimal('target_amount', 15, 2)->default(0.00);
            $table->timestamps();

            // Ensure each month has only one target
            $table->unique(['year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_targets');
    }
};
