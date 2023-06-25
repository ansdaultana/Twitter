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
        $ansnazir = User::factory()->create(['id'=>3,'name' => 'Ans Nazir', 'username' => 'ansnazir', 'email' => 'ansdaultana@gmail.com', 'password' => '12121212']);
        $leo = User::factory()->create(['id'=>4,'name' => 'Leonardo Dicaprio', 'username' => 'leonardodicaprio', 'email' => 'leonardodicaprio@twitter.com', 'password' => '12121212']);
       Tweet::factory()->create(["id" => 38 ,"user_id"=>1,"parent_tweet_id"=>NULL,"text"=>'Succession Finale!!!!!!!!!!!!!!!!!!!!"',"image"=>"https://res.cloudinary.com/ddrivhxfq/image/upload/v1687185190/xvh0fi8e4zictp5aizl3.png"]);
       Tweet::factory()->create(["id"=>2 ,"user_id"=>2,"parent_tweet_id"=>38,"text"=>'favourite Series!!!!!!!!!!!!!!!',"image"=>"https://res.cloudinary.com/ddrivhxfq/image/upload/v1687186726/cb3k7udezss4gzllhdd0.jpg"]);
       Tweet::factory()->create(["id"=>46 ,"user_id"=>2,"text"=>"Top G !!!!!!!!!!!!!!!","video"=>"https://res.cloudinary.com/ddrivhxfq/video/upload/v1687187639/j8va2t6fqocfqch5m3ml.mp4"]);
       Tweet::factory()->create(["id"=>1 ,"user_id"=>1,"text"=>"We Go Gym!!!!!!!!!!!!!!!!!!","image"=>"https://res.cloudinary.com/ddrivhxfq/image/upload/v1687244159/ampvpmadasvjwkcqetkx.jpg"]);

       Tweet::factory()->create(["id"=>3 ,"user_id"=>1,"text"=>"swipe up","image"=>"https://res.cloudinary.com/ddrivhxfq/image/upload/v1687256055/uq1ms6dwa9ei3iud4mxw.jpg"]);
       Tweet::factory()->create(["id"=>5 ,"user_id"=>1,"text"=>"david goggins","video"=>"https://res.cloudinary.com/ddrivhxfq/video/upload/v1687257258/hugeilbxijahzuwyhd1g.mp4"]);
       Tweet::factory()->create(["id"=>7 ,"user_id"=>1,"text"=>"Succession!!!!!","image"=>"https://res.cloudinary.com/ddrivhxfq/image/upload/v1687257912/xfei72zexz0onc240qcz.jpg"]);
      
       Tweet::factory()->create(["id"=>8 ,"user_id"=>1,"text"=>"Some of my favorites ❤️","image"=>"https://res.cloudinary.com/ddrivhxfq/image/upload/v1687261779/vpqu3zfsgdckt1wceqnc.png"]);
                
        // User::factory(9)->create();

    

        // $users->each(function ($user) {
        //     Tweet::factory()
        //         ->count(5)
        //         ->withCommentsAndLikes()
        //         ->create(['user_id' => $user->id]);
        // });

    }
}