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
        Schema::create('priority_product_budgets', function (Blueprint $table) {
        $table->id();
        $table->year('year');
        $table->unsignedTinyInteger('month'); // 1-12
        $table->decimal('extra_budget', 15, 2)->default(0);
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('priority_product_budgets');
    }
};
