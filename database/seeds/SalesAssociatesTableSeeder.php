<?php

use Illuminate\Database\Seeder;

class SalesAssociatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('sales_associates')->insert([
            'sa_id'            => '1',
            'first_name'        => 'Michael',
            'last_name'         => 'Selby',
            'email'             => 'michaelselbygh@gmail.com',
            'phone'             => '233503788515',
            'password'          => bcrypt('SMichael2131$'),
            'address'           => 'New Legon PLT 87, Adenta',
            'badge'             => '1',
            'id_type'           => 'Voters ID',
            'id_file'           => 'SNI23-U304S-J20RT-W19002.pdf',
            'mode_of_payment'   => 'Vodafone Cash',
            'payment_details'   => '0503788515 - Michael Selby',
            'balance'          => '0',
            'created_at'        => \Carbon\Carbon::now(),
            'updated_at'        => \Carbon\Carbon::now(),
        ]);
    }
}
