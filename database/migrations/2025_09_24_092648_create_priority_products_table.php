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
        Schema::create('priority_products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Product/Project name
            $table->integer('quantity')->default(1);; // Quantity of the product
            $table->decimal('amount', 10, 2); // Total amount
            $table->text('description')->nullable(); // Description or note about the product/project
            $table->boolean('is_purchased')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('priority_products');
    }
};
