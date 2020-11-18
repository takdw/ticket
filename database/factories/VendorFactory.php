<?php

namespace Database\Factories;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class VendorFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vendor::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'tin' => Str::random(6),
            'logo_path' => 'logos/logo.jpg',
            'image_path' => 'images/image.jpg',
            'license_path' => 'licenses/license.jpg',
        ];
    }
}
