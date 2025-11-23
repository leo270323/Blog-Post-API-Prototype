<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition()
    {
        return [
            'title'      => $this->faker->sentence(6),
            'content'    => $this->faker->paragraph(3),
            'author_id'  => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
