<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
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
            'date' => now()->addDays($this->faker->numberBetween(10, 200)),
            'venue' => $this->faker->word,
            'city' => $this->faker->city,
            'price' => 2500,
            'vendor_id' => function() {
                return Vendor::factory()->create()->id;
            },
            'additional_info' => $this->faker->sentence,
            'poster' => UploadedFile::fake()->image('poster.jpg'),
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
