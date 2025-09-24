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
        Schema::create('monthly_online_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('year');
            $table->unsignedInteger('month');
            $table->unsignedBigInteger('category_id');
            $table->date('activate_date')->nullable();
            $table->date('expire_date')->nullable();
            $table->enum('activate_type', ['dollar', 'taka'])->default('dollar');
            $table->decimal('activate_cost', 10, 2)->nullable();
            $table->enum('amount_type', ['dollar', 'taka'])->default('dollar');
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('description')->nullable();
            $table->date('paid_date')->nullable();
            $table->enum('status', ['paid', 'unpaid'])->default('unpaid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_online_costs');
    }
};
