<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Faker\Factory as faker;
use Illuminate\Support\Facades\Hash;

class users extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        $faker=faker::create();
        foreach(range(1,1500) as $i){
            User::create([
                'name'=>$faker->name(),
                'email'=>$faker->unique()->email(),
                'mobile'=>$faker->phoneNumber,    
                'password'=>Hash::make($faker->password()),         
                'profile_img'=>$faker->image(storage_path('app\public\media'),400,400,null,false),
            ]);         
        }
    }
}
