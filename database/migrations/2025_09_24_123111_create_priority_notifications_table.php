<?php 

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('priority_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('priority_product_id')->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(true); // Active only if condition still valid
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('priority_notifications');
    }
};
