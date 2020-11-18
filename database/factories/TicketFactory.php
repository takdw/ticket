<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'price' => 2500,
            'vendor_id' => function() {
                return Vendor::factory()->create()->id;
            }
        ];
    }
}
