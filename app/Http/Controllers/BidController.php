<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Bid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BidController extends Controller
{
    public function store(Request $request, Auction $auction)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $userId = $request->user()->id;
        $amount = $request->input('amount');

        DB::transaction(function () use ($auction, $amount, $userId) {
            $locked = Auction::where('id', $auction->id)->lockForUpdate()->first();

            // Server-authoritative check — never trust status flag alone or client clock
            if ($locked->status !== 'active' || now()->gte($locked->ends_at)) {
                abort(422, 'This auction is no longer accepting bids');
            }

            if ($locked->user_id === $userId) {
                abort(403, 'Cannot bid on your own auction');
            }

            if ($amount < $locked->current_price + $locked->min_increment) {
                abort(422, 'Bid must be at least the minimum increment above current price');
            }

            $expectedVersion = $locked->version;

            $updated = Auction::where('id', $locked->id)
                ->where('version', $expectedVersion)
                ->update([
                    'current_price' => $amount,
                    'current_bidder_id' => $userId,
                    'version' => $expectedVersion + 1,
                ]);

            if ($updated === 0) {
                abort(409, 'Auction was modified concurrently, please retry');
            }

            Bid::create([
                'auction_id' => $locked->id,
                'user_id' => $userId,
                'amount' => $amount,
                'placed_at' => now(),
            ]);
        });

        return back()->with('success', 'Bid placed!');
    }
}