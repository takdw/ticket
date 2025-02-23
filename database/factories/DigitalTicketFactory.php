<?php

namespace Database\Factories;

use App\Models\DigitalTicket;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

class DigitalTicketFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = DigitalTicket::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => 'EXAMPLE_CODE',
            'order_id' => function () {
                return Order::factory()->create()->id;
            },
        ];
    }
}
