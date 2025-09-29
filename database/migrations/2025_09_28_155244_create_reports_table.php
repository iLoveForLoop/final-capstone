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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            // Reporter (can be vendor or client)
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade');
            $table->enum('reporter_type', ['vendor', 'client']);

            // Reported user (can be vendor or client)
            $table->foreignId('reported_id')->constrained('users')->onDelete('cascade');
            $table->enum('reported_type', ['vendor', 'client']);

            // Dynamic report details
            $table->string('reason'); // Free text or predefined, your choice
            $table->text('description');

            // Status
            $table->enum('status', ['pending', 'reviewed', 'resolved', 'dismissed'])->default('pending');

            // Timestamps
            $table->timestamps();


            // Add composite index for the rate limiting query
            $table->index(['reporter_id', 'reported_id', 'created_at']);

            // Indexes for better performance
            $table->index(['reporter_id', 'reporter_type']);
            $table->index(['reported_id', 'reported_type']);
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};