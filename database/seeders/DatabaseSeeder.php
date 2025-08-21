<?php

namespace Database\Seeders;

use App\Models\StoragePrice;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Table need truncate
     */
    protected $toTruncates = [
        \App\Models\MRequestType::class,
        \App\Models\UnitPrice::class,
        \App\Models\MTax::class,
        \App\Models\StoragePrice::class,
        // \App\Models\WarehouseArea::class,
        // \App\Models\Warehouse::class,
        // \App\Models\Package::class,
        // \App\Models\PackageGroup::class,
        // \App\Models\User::class,
        // \App\Models\UserRequest::class,
        // \App\Models\UserProfile::class,
        // \App\Models\RequestPackageImage::class,
        // \App\Models\RequestPackageTracking::class,
        // \App\Models\RequestPackage::class,
        // \App\Models\RequestPackageGroup::class,
    ];

    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        foreach ($this->toTruncates as $model) {
            $model::truncate();
        }

        $this->call([
            // UserSeeder::class,
            // UserProfileSeeder::class,
            MRequestTypeSeeder::class,
            UnitPriceSeeder::class,
            MTaxSeeder::class,
            StoragePriceSeeder::class,
            // WarehouseSeeder::class,
            // WarehouseAreaSeeder::class,
            // PackageGroupSeeder::class,
            // PackageSeeder::class,
            // UserRequestSeeder::class
            // RequestPackageGroupSeeder::class,
            // RequestPackageSeeder::class,
            // RequestPackageImageSeeder::class,
            // RequestPackageTrackingSeeder::class,
        ]);

        Schema::enableForeignKeyConstraints();
    }
}
