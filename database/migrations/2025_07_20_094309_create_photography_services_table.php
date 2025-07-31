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
        Schema::create('photography_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');

            $table->string('coverage_type'); // e.g., Wedding, Corporate
            $table->integer('hours_of_coverage')->nullable();
            $table->integer('delivery_time_days')->nullable();
            $table->json('deliverables')->nullable(); // e.g., ["Photo Album", "USB"]
            $table->integer('number_of_photographers')->nullable();

            $table->json('specifications')->nullable(); // bullet points or extras
            $table->text('notes')->nullable();

            $table->boolean('studio_shoot_available')->default(false); // ✅ New field

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photography_services');
    }
};
