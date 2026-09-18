<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->timestamp('placed_at'); // set server-side in the controller, not client input
            $table->timestamps();

            $table->index(['auction_id', 'amount']); // fast "highest bid" lookups
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};