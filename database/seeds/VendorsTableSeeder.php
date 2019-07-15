<?php

use Illuminate\Database\Seeder;

class VendorsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('vendors')->insert([
            'vendor_id'     => '1',
            'name'          => 'Solushop Core',
            'username'      => 'SolushopCore',
            'email'         => 'michaelselbygh@gmail.com',
            'phone'         => '233506753093',
            'alt_phone'     => '233503788515',
            'password'      => bcrypt('VMichael2131$'),
            'address'       => 'Solushop Hub, Eastlegon',
            'balance'       => '0',
            'created_at'    => \Carbon\Carbon::now(),
            'updated_at'    => \Carbon\Carbon::now(),
        ]);
    }
}
