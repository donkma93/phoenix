<?php

namespace Database\Factories;

use App\Models\PackageGroup;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackageGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PackageGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::where('role', User::ROLE_USER)->inRandomOrder()->first()->id,
            'name' => $this->faker->name,
            'barcode' =>  $this->faker->unique()->ean13,
            'file' => $this->faker->imageUrl(),
            'unit_width' =>  rand(0, 1) ? $this->faker->randomFloat(10, 0, 100) : null,
            'unit_weight' =>  rand(0, 1) ? $this->faker->randomFloat(10, 0, 100) : null,
            'unit_height' =>  rand(0, 1) ? $this->faker->randomFloat(10, 0, 100) : null,
            'unit_length' =>  rand(0, 1) ? $this->faker->randomFloat(10, 0, 100) : null,
        ];
    }
}
