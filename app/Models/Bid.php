<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bid extends Model
{
    protected $fillable = ['auction_id', 'user_id', 'amount', 'placed_at'];

    protected $casts = [
        'amount' => 'decimal:2',
        'placed_at' => 'datetime',
    ];

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    public function bidder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}