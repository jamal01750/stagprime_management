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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_category_id')
                ->constrained('product_categories')
                ->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->enum('amount_type', ['dollar', 'taka'])->default('dollar'); // Default to dollar
            $table->decimal('amount', 10, 2)->default(0.00); // Default to 0.00
            $table->enum('status', ['approved', 'pending'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
