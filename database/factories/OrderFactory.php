<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => function() {
                return User::factory()->create()->id;
            },
            'confirmation_number' => 'EXAMPLE_CONFIRMATION_NUMBER',
            'amount' => 12000,
        ];
    }
}
