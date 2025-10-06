<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->onDelete('cascade');
            $table->decimal('installment_amount', 15, 2);
            $table->decimal('due_amount', 15, 2);
            $table->date('pay_date');
            $table->date('next_date')->nullable();
            $table->enum('approve_status', ['pending', 'approved'])->default('pending');
            $table->enum('status', ['unpaid','paid'])->default('unpaid');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installments');
    }
};
