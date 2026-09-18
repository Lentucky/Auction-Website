<?php

namespace Tests\Feature;

use App\Models\Auction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BidControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_place_valid_bid(): void
    {
        $artist = User::factory()->create();
        $bidder = User::factory()->create();

        $auction = Auction::create([
            'user_id' => $artist->id,
            'title' => 'Test YCH',
            'description' => 'Test slot',
            'type' => 'ych',
            'content_rating' => 'safe',
            'starting_price' => 10,
            'current_price' => 10,
            'min_increment' => 5,
            'starts_at' => now(),
            'ends_at' => now()->addDay(),
            'status' => 'active',
        ]);

        $response = $this->actingAs($bidder)
            ->post("/auctions/{$auction->id}/bids", ['amount' => 15]);

        $response->assertSessionHas('success');
        $this->assertEquals(15, $auction->fresh()->current_price);
        $this->assertEquals($bidder->id, $auction->fresh()->current_bidder_id);
    }

    public function test_bid_below_increment_fails(): void
    {
        $artist = User::factory()->create();
        $bidder = User::factory()->create();

        $auction = Auction::create([
            'user_id' => $artist->id,
            'title' => 'Test YCH',
            'description' => 'Test slot',
            'type' => 'ych',
            'content_rating' => 'safe',
            'starting_price' => 10,
            'current_price' => 10,
            'min_increment' => 5,
            'starts_at' => now(),
            'ends_at' => now()->addDay(),
            'status' => 'active',
        ]);

        $response = $this->actingAs($bidder)
            ->post("/auctions/{$auction->id}/bids", ['amount' => 12]);

        $response->assertStatus(422);
    }
}