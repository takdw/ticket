<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

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
            'title' => $this->faker->sentence,
            'subtitle' => $this->faker->sentence,
            'date' => Carbon::parse('20-10-2020 12:00PM'),
            'venue' => $this->faker->word,
            'city' => $this->faker->city,
            'price' => 2500,
            'vendor_id' => function() {
                return Vendor::factory()->create()->id;
            },
            'additional_info' => $this->faker->sentence,
        ];
    }

    public function published()
    {
        return $this->state(function (array $attributes) {
            return [
                'published_at' => now(),
            ];
        });
    }

    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'published_at' => now(),
                'approved_at' => now(),
            ];
        });
    }
}
