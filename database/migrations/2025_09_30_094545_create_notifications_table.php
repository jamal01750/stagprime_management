<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->morphs('notifiable'); // For polymorphic relationship (e.g., MonthlyOfflineCost, ClientProjectDebit)
            $table->enum('type', ['pay', 'collect', 'info']);
            $table->enum('level', ['red', 'blue', 'green']);
            $table->text('message');
            $table->date('due_date')->nullable();
            $table->integer('days_left')->nullable();
            $table->string('action_route')->nullable();
            $table->json('action_params')->nullable();
            $table->enum('status', ['active', 'cleared'])->default('active');
            $table->timestamp('cleared_at')->nullable();
            $table->timestamp('generated_at')->useCurrent(); // <-- CHANGE HERE
            $table->timestamp('updated_level_at')->useCurrent(); // <-- CHANGE HERE
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
