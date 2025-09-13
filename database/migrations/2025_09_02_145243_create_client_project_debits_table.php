<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('client_project_debits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('client_projects')->onDelete('cascade');
            $table->enum('currency', ['Dollar', 'Taka'])->default('Dollar');
            $table->decimal('pay_amount', 15, 2);
            $table->decimal('due_amount', 15, 2);
            $table->date('pay_date');
            $table->date('next_date')->nullable();
            $table->enum('status', ['unpaid', 'paid'])->default('unpaid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_project_debits');
    }
};
