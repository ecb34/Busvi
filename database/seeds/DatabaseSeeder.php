<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(SectorsTableSeeder::class);
        $this->call(PostsTableSeeder::class);
        // $this->call(AddDummyEvent::class);
    }
}
