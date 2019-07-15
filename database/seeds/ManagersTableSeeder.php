<?php

use Illuminate\Database\Seeder;

class ManagersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('managers')->insert([
            'manager_id'    => '1',
            'first_name'    => 'Michael',
            'last_name'     => 'Selby',
            'email'         => 'michaelselbygh@gmail.com',
            'phone'         => '233503788515',
            'password'      => bcrypt('MMichael2131$'),
            'sms'           => '0',
            'access_level'  => '0',
            'avi'           => 'michael.jpg',
            'created_at'    => \Carbon\Carbon::now(),
            'updated_at'    => \Carbon\Carbon::now(),
        ]);
    }
}
