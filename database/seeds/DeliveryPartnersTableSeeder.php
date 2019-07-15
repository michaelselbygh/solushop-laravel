<?php

use Illuminate\Database\Seeder;

class DeliveryPartnersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('delivery_partners')->insert([
            'dp_id'         => '1',
            'dp_company_id' => '1',
            'first_name'    => 'Michael',
            'last_name'     => 'Selby',
            'email'         => 'michaelselbygh@gmail.com',
            'password'      => bcrypt('DMichael2131$'),
            'access_level'  => '0',
            'created_at'    => \Carbon\Carbon::now(),
            'updated_at'    => \Carbon\Carbon::now(),
        ]);
    }
}
