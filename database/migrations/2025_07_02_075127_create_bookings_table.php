<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');      // Organizer
            $table->foreignId('event_id')->constrained()->onDelete('cascade');     // Event
            $table->foreignId('service_id')->constrained()->onDelete('cascade');   // Booked service
            $table->date('booking_date');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed', 'declined'])->default('pending');
            //for catering specifics only
            $table->json('catering_dishes')->nullable();
            $table->integer('pax')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}