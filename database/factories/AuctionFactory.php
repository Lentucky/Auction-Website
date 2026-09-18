<?php
namespace Database\Factories;

use App\Models\Auction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuctionFactory extends Factory
{
    protected $model = Auction::class;

    public function definition(): array
    {
        $startPrice = fake()->randomFloat(2, 10, 100);

        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'type' => 'ych',
            'content_rating' => 'safe',
            'starting_price' => $startPrice,
            'current_price' => $startPrice,
            'min_increment' => 5.00,
            'starts_at' => now(),
            'ends_at' => now()->addHours(24),
            'status' => 'active',
        ];
    }
}