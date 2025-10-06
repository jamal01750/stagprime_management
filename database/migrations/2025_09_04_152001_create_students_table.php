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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('student_id')->unique();
            $table->string('student_name');
            $table->string('phone', 11);
            $table->string('alt_Phone', 11)->nullable();
            $table->bigInteger('nid_birth');
            $table->text('address')->nullable();
            $table->unsignedBigInteger('batch_id');
            $table->unsignedBigInteger('course_id');
            $table->string('batch_time');
            $table->date('admission_date');
            $table->decimal('total_fee', 10, 2);
            $table->decimal('paid_amount', 10, 2);
            $table->decimal('due_amount', 10, 2)->default(0);
            $table->date('payment_due_date')->nullable();
            $table->text('description')->nullable();
            $table->enum('payment_status', ['Paid', 'Unpaid'])->default('Unpaid');
            $table->enum('active_status', ['Running', 'Expired'])->default('Running');
            $table->enum('approve_status', ['pending', 'approved'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
