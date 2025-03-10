<?php

use Illuminate\Database\Seeder;
use App\Event;

class AddDummyEvent extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
        	[
                'title' => 'Demo Event-1',
                'user_id' => 4,
                'customer_id' => 5,
                'service_id' => 1,
                'start_date' => '2018-04-11 10:30:00',
                'end_date' => '2018-04-12 11:00:00'
            ],
        	[
                'title' => 'Demo Event-2',
                'user_id' => 4,
                'customer_id' => 5,
                'service_id' => 1,
                'start_date' => '2018-04-11 10:30:00',
                'end_date' => '2018-04-13 11:00:00'
            ],
        	[
                'title' => 'Demo Event-3',
                'user_id' => 4,
                'customer_id' => 5,
                'service_id' => 1,
                'start_date' => '2018-04-14 10:30:00',
                'end_date' => '2018-04-14 11:00:00'
            ],
        	[
                'title' => 'Demo Event-3',
                'user_id' => 4,
                'customer_id' => 5,
                'service_id' => 1,
                'start_date' => '2018-04-17 10:30:00',
                'end_date' => '2018-04-17 11:00:00'
            ],
        ];

        foreach ($data as $key => $value)
        {
        	Event::create($value);
        }
    }
}
