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
        Schema::create('internship_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable();
            $table->string('intern_id')->unique();
            $table->string('internee_name');
            $table->string('phone');
            $table->string('alt_Phone')->nullable();
            $table->string('nid_birth')->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('batch_id');
            $table->unsignedBigInteger('course_id');
            $table->string('batch_time');
            $table->date('admission_date');
            $table->decimal('pay_amount', 10, 2);
            $table->decimal('total_paid', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->enum('payment_status', ['Full paid', 'Partial'])->default('Partial');
            $table->enum('active_status', ['Running', 'Expired'])->default('Running');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internship_registrations');
    }
};
