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
        // Schema::create('catering_services', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
        //     $table->string('cuisine_type')->nullable();
        //     $table->integer('min_pax')->default(0);
        //     $table->integer('max_pax')->nullable();
        //     $table->decimal('price_per_head', 10, 2)->nullable();
        //     $table->integer('lead_time_days')->default(3);
        //     $table->json('service_area')->nullable();
        //     $table->boolean('is_customizable')->default(false);
        //     $table->timestamps();
        // });

        Schema::create('catering_services', function (Blueprint $table) {
            $table->id();

            // ✅ Shorthand for foreign keys
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');


            // Basic Info
            $table->string('name');
            $table->integer('min_pax');
            $table->integer('max_pax');
            $table->decimal('price', 10, 2);
            $table->decimal('package_price', 10, 2)->nullable();
            $table->integer('lead_time_days')->default(3);
            $table->json('service_area')->nullable();
            $table->boolean('is_customizable')->default(false);

            // Optional Details
            $table->decimal('delivery_fee', 8, 2)->nullable();
            $table->string('buffet_type')->nullable();

            // Specifications
            $table->json('specifications')->nullable();
            $table->json('dishes')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catering_services');
    }
};
