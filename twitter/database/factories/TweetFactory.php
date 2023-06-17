<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Tweet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tweet>
 */
class TweetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'user_id' => User::factory(),
            'text' => $this->faker->paragraph(5),
        ];
    }
 
    public function withCommentsAndLikes() 
    {
        return $this->afterCreating(function (Tweet $tweet) {
            $comments = Comment::factory()
                ->count(5)
                ->create([
                    'tweet_id' => $tweet->id,
                ]);
            foreach ($comments as $comment) {
                Like::factory()
                    ->count(random_int(3, 100))
                    ->create([
                        'likeable_id' => $comment->id,
                        'likeable_type' => Comment::class,
                    ]);
            }

            Like::factory()
                ->count(random_int(3, 100))
                ->create([
                    'likeable_id' => $tweet->id,
                    'likeable_type' => Tweet::class,
                ]);
        });
    }
    
}
