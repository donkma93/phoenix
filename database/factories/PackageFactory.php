<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\PackageGroup;
use App\Models\User;
use App\Models\WarehouseArea;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Package::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::where('role', User::ROLE_USER)->inRandomOrder()->first()->id,
            'unit_number' => rand(10, 20),
            'received_unit_number' => rand(0, 1) ? rand(0, 10) : null,

            'width' => rand(0, 1) ? $this->faker->randomFloat(10, 0, 100) : null,
            'weight' => rand(0, 1) ? $this->faker->randomFloat(10, 0, 100) : null,
            'height' => rand(0, 1) ? $this->faker->randomFloat(10, 0, 100) : null,
            'length' => rand(0, 1) ? $this->faker->randomFloat(10, 0, 100) : null,

            'package_group_id' => PackageGroup::inRandomOrder()->first()->id,
            'status' => array_rand(Package::$statusName, 1),
            'warehouse_area_id' => rand(0, 1) ? WarehouseArea::inRandomOrder()->first()->id : null,
            'barcode' =>  $this->faker->unique()->ean13,
            'unit_barcode' => $this->faker->unique()->ean13

            // 'name' => $this->faker->name,
        ];
    }
}
