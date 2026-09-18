<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Auction extends Model
{

    protected $fillable = [
        'user_id', 'title', 'description', 'type', 'content_rating',
        'starting_price', 'current_price', 'min_increment',
        'starts_at', 'ends_at', 'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'starting_price' => 'decimal:2',
        'current_price' => 'decimal:2',
        'min_increment' => 'decimal:2',
    ];

    public function artist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function currentBidder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_bidder_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class)->orderByDesc('amount');
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && now()->lt($this->ends_at);
    }
}