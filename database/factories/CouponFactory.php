<?php

namespace Database\Factories;

use App\Helpers\CouponHelper;
use App\Models\Bundle;
use Illuminate\Database\Eloquent\Factories\Factory;

class CouponFactory extends Factory
{
    public function definition(): array
    {
        return [
            'bundle_id' => Bundle::factory(),
            'code' => strtoupper(fake()->unique()->bothify('??????##')),
            'discount_amount' => fake()->randomFloat(2, 5, 100),
            'receiver_name' => fake()->name(),
            'receiver_email' => fake()->unique()->safeEmail(),
            'send_date' => null,
            'send_immediately' => false,
            'expires_at' => now()->addMonth(),
            'is_used' => false,
            'used_at' => null,
            'email_sent_at' => null,
            'last_sent_at' => null,
            'subscribed' => true,
        ];
    }
}
