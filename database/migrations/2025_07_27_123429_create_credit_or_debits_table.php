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
        Schema::create('credit_or_debits', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('amount', 20, 2);
            $table->string('description')->nullable();
            $table->enum('approve_status', ['pending', 'approved'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_or_debits');
    }
};
