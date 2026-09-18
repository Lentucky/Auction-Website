<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // the artist
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['ych', 'commission_slot', 'adoptable', 'base_recolor']);
            $table->enum('content_rating', ['safe', 'questionable', 'explicit']);
            $table->decimal('starting_price', 10, 2);
            $table->decimal('current_price', 10, 2);
            $table->decimal('min_increment', 10, 2)->default(1.00);
            $table->foreignId('current_bidder_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('starts_at');
            $table->timestamp('ends_at'); // server-authoritative, never trust client time
            $table->enum('status', ['draft', 'active', 'closed', 'cancelled'])->default('draft');
            $table->unsignedInteger('version')->default(0); // optimistic locking for bid races
            $table->timestamps();

            $table->index(['status', 'ends_at']); // fast lookup for "auctions ending soon" jobs
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auctions');
    }
};