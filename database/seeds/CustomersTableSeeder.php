<?php

use Illuminate\Database\Seeder;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('customers')->insert([
            'customer_id'       => 'C240720170001',
            'first_name'        => 'Michael',
            'last_name'         => 'Selby',
            'email'             => 'michaelselbygh@gmail.com',
            'email_verified'    => '0',
            'phone'             => '233503788515',
            'phone_verified'    => '0',
            'activation_code'   => '2131',
            'password'          => bcrypt('CMichael2131$'),
            'default_address'   => '34',
            'date_of_birth'     => '1997-03-13',
            'milkshake'         => '6',
            'icono'             => NULL,
            'sm'                => 'SOLUSHOP',
            'created_at'        => \Carbon\Carbon::now(),
            'updated_at'        => \Carbon\Carbon::now(),
        ]);
    }
}
