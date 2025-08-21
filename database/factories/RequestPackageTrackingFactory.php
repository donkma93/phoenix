<?php

namespace Database\Factories;

use App\Models\RequestPackageGroup;
use App\Models\RequestPackageTracking;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestPackageTrackingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RequestPackageTracking::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'request_package_group_id' => RequestPackageGroup::inRandomOrder()->first()->id,
            'tracking_url' => $this->faker->url,
        ];
    }
}
