<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Str as Str;

class PostsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        for($i = 0; $i < 20; $i++)
        {
            $title = $faker->sentence;
            $body  = $faker->text(3000);
            $slug  = Str::slug($title);
            
            \DB::table('posts')->insert(array(
                'title' => $title,
                'body'  => $body,
                'slug'  => $slug
            ));
        }
    }
}
