<?php

namespace Database\Factories;

use App\Models\PackageGroup;
use App\Models\RequestPackageGroup;
use App\Models\UserRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class RequestPackageGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = RequestPackageGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_request_id' => UserRequest::inRandomOrder()->first()->id,
            'package_group_id' => PackageGroup::inRandomOrder()->first()->id,
            'barcode' => $this->faker->unique()->ean13
        ];
    }
}
