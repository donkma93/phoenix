<?php

namespace Database\Factories;

use App\Models\RequestPackageGroup;
use App\Models\RequestPackageImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestPackageImageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RequestPackageImage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'request_package_group_id' => RequestPackageGroup::inRandomOrder()->first()->id,
            'image_url' => $this->faker->imageUrl(),
        ];
    }
}
