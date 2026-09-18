<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuctionController extends Controller
{
    public function index(Request $request)
    {
        $query = Auction::query()
            ->with('artist:id,name')
            ->where('status', 'active');

        // Filter by type (ych, commission_slot, adoptable, base_recolor)
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // Filter by content rating
        if ($request->filled('rating')) {
            $query->where('content_rating', $request->input('rating'));
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('current_price', '>=', $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('current_price', '<=', $request->input('max_price'));
        }

        // Sorting
        $sort = $request->input('sort', 'ending_soon');
        match ($sort) {
            'ending_soon' => $query->orderBy('ends_at', 'asc'),
            'newest' => $query->orderBy('created_at', 'desc'),
            'price_low' => $query->orderBy('current_price', 'asc'),
            'price_high' => $query->orderBy('current_price', 'desc'),
            default => $query->orderBy('ends_at', 'asc'),
        };

        $auctions = $query->paginate(12)->withQueryString();

        return Inertia::render('Auctions/Index', [
            'auctions' => $auctions,
            'filters' => $request->only(['type', 'rating', 'min_price', 'max_price', 'sort']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Auctions/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:ych,commission_slot,adoptable,base_recolor',
            'content_rating' => 'required|in:safe,questionable,explicit',
            'starting_price' => 'required|numeric|min:0.01',
            'min_increment' => 'required|numeric|min:0.01',
            'ends_at' => 'required|date|after:now',
        ]);

        $auction = Auction::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'content_rating' => $validated['content_rating'],
            'starting_price' => $validated['starting_price'],
            'current_price' => $validated['starting_price'], // starts equal to starting price
            'min_increment' => $validated['min_increment'],
            'starts_at' => now(),
            'ends_at' => $validated['ends_at'],
            'status' => 'active',
        ]);

        return redirect()->route('auctions.show', $auction)->with('success', 'Auction created!');
    }

    public function show(Auction $auction)
    {
        $auction->load('artist:id,name', 'bids.bidder:id,name');
        return Inertia::render('Auctions/Show', ['auction' => $auction]);
    }

    public function edit(Auction $auction)
    {
        $this->authorize('update', $auction);

        return Inertia::render('Auctions/Edit', ['auction' => $auction]);
    }

    public function update(Request $request, Auction $auction)
    {
        $this->authorize('update', $auction);

        // Block edits once bidding has started — changing price/terms mid-auction isn't fair to bidders
        if ($auction->bids()->exists()) {
            abort(422, 'Cannot edit an auction that already has bids. Cancel it instead.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:ych,commission_slot,adoptable,base_recolor',
            'content_rating' => 'required|in:safe,questionable,explicit',
            'starting_price' => 'required|numeric|min:0.01',
            'min_increment' => 'required|numeric|min:0.01',
            'ends_at' => 'required|date|after:now',
        ]);

        $auction->update([
            ...$validated,
            'current_price' => $validated['starting_price'], // no bids yet, safe to reset
        ]);

        return redirect()->route('auctions.show', $auction)->with('success', 'Auction updated!');
    }

    public function cancel(Auction $auction)
    {
        $this->authorize('delete', $auction);

        $auction->update(['status' => 'cancelled']);

        return redirect()->route('auctions.show', $auction)->with('success', 'Auction cancelled.');
    }
}