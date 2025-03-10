<?php

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class SectorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	for ($i = 0; $i < 3; $i++)
    	{
    		$faker = Faker::create();
    		$sector = new \App\Sector();

    		$sector->name = $faker->word;
    		$sector->sector_parent_id = 0;

    		$sector->save();
    	}
    }
}
