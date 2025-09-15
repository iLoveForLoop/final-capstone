<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->enum('type', ['direct', 'support'])->default('direct');
            $table->unsignedBigInteger('event_id')->nullable(); // Link to specific event
            $table->json('participants'); // Store user IDs as JSON array
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->index(['event_id', 'last_message_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('conversations');
    }
};