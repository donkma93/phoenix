<?php

namespace Database\Factories;

use App\Models\MRequestType;
use App\Models\Package;
use App\Models\PackageGroup;
use App\Models\User;
use App\Models\UserRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $package = Package::inRandomOrder()->first();

        $factory = [
            'user_id' => $package->user_id,
            'status' => array_rand(UserRequest::$statusName, 1),
        ];

        $request = MRequestType::inRandomOrder()->first();
        $factory['m_request_type_id'] = $request->id;
        $factory['note'] =  $this->faker->realText();

        if ($request->name == 'warehouse labor') {
            $factory['option'] = array_rand(UserRequest::$optionName, 1);
            // TODO image barcode or pdf
            $factory['file'] =  $this->faker->imageUrl(640, 480);
        }

        if ($request->name == 'relabel' || $request->name == 'outbound') {
            // TODO image barcode or pdf
            $factory['file'] =  $this->faker->imageUrl(640, 480);
        }

        if (!in_array($request->name, ["add package", "return", "removal"])) {
            $factory['packages'] = $package->id;
        }

        if ($factory['status'] != UserRequest::STATUS_NEW) {
            $factory['staff_id'] = User::whereIn('role', [User::ROLE_PICKER, User::ROLE_PACKER, User::ROLE_RECEIVER, User::ROLE_STAFF])->inRandomOrder()->first()->id;
            $factory['start_at'] = Carbon::now()->subDays(rand(15, 30));
        }

        if ($factory['status'] == UserRequest::STATUS_DONE) {
            $factory['finish_at'] = Carbon::now()->subDays(rand(1, 15));
        }

        return $factory;
    }
}
