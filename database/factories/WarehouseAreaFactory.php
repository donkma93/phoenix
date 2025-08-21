<?php

namespace Database\Factories;

use App\Models\WarehouseArea;
use Illuminate\Database\Eloquent\Factories\Factory;

class WarehouseAreaFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WarehouseArea::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'warehouse_id' => \App\Models\Warehouse::inRandomOrder()->first()->id
        ];
    }
}
