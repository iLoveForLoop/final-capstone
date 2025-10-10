<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('type');
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->string('priority')->default('normal');
            $table->string('action_url')->nullable();
            $table->timestamps();

            // New for clients
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('recipient_type')->default('vendor'); // 'vendor' or 'client'

            // Updated indexes for better performance
            $table->index(['vendor_id', 'created_at']);
            $table->index(['vendor_id', 'read_at']);
            $table->index(['vendor_id', 'type']);
            $table->index(['client_id', 'created_at']);
            $table->index(['client_id', 'read_at']);
            $table->index(['recipient_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
