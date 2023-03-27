<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // or ($i=0; $i < 100; $i++) { 
        //     $user = new \Modules\Users\Entities\User;
        //     $user->userable_id = 0;
        //     $user->userable_type = "Modules\Employees\Entities\Employee";
        //     $user->email = '059403442' + $i;
        //     $user->password = \Illuminate\Support\Facades\Hash::make("123456789");
        //     $user->save();
        // }f
            // $user->assignRole($row['roles']);
        $this->call([
            // UserSeeder::class,
            // CoreSeeder::class,
            AdminstatusForVendorActivitySeeder::class
        ]);

    }
}
