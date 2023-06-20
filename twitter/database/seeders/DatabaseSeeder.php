<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Tweet;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        $admin = User::factory()->create(['id'=>1,'name' => 'Ans Daultana', 'username' => 'ansdaultana', 'email' => 'ansdaultana.ad5@gmail.com', 'password' => '12121212']);
        $elon = User::factory()->create(['id'=>2,'name' => 'Elon Musk', 'username' => 'elonmusk', 'email' => 'elonmusk@twitter.com', 'password' => '12121212']);
       
        // User::factory(9)->create();

        Tweet::factory()
            ->count(5)
            ->create(['user_id' => $admin->id]);
            Tweet::factory()
            ->count(5)
            ->create(['user_id' => $elon->id]);

        // $users->each(function ($user) {
        //     Tweet::factory()
        //         ->count(5)
        //         ->withCommentsAndLikes()
        //         ->create(['user_id' => $user->id]);
        // });

    }
}