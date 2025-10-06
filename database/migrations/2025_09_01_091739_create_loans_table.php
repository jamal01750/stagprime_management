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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_name');
            $table->decimal('loan_amount', 15, 2);
            $table->integer('installment_number');
            $table->enum('installment_type', ['month', 'week'])->default('month');
            $table->decimal('installment_amount', 15, 2);
            $table->decimal('due_amount', 15, 2);
            $table->enum('approve_status', ['pending', 'approved'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
