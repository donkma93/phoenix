<?php

namespace Database\Factories;

use App\Models\RequestPackage;
use App\Models\RequestPackageGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestPackageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RequestPackage::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'request_package_group_id' => RequestPackageGroup::inRandomOrder()->first()->id,
            'package_number' => rand(10, 20),
            'received_package_number' => rand(0, 10),
            'unit_number' => rand(20, 40),
            'received_unit_number' => rand(0, 20),
            'width' => rand(0, 1) ? $this->faker->randomFloat(10, 0, 100) : null,
            'weight' => rand(0, 1) ? $this->faker->randomFloat(10, 0, 100) : null,
            'height' => rand(0, 1) ? $this->faker->randomFloat(10, 0, 100) : null,
            'length' => rand(0, 1) ? $this->faker->randomFloat(10, 0, 100) : null,
            'barcode' => $this->faker->unique()->ean13,
        ];
    }
}
