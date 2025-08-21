<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $gender = array_rand(UserProfile::$genderName, 1);
        $now = Carbon::now();
        return [
            'user_id' => User::doesntHave('profile')->inRandomOrder()->first()->id,
            'first_name' => $gender == UserProfile::GENDER_MALE ? $this->faker->firstNameMale :  $this->faker->firstNameFemale,
            'last_name' => $this->faker->lastName,
            'birthday' =>  $now->subYears(rand(1, 40)),
            'membership_at' => $now->subDays(rand(1, 30)),
            'avatar' => $this->faker->imageUrl(),
            'phone' => $this->faker->phoneNumber,
            'gender' => $gender,
        ];
    }
}
