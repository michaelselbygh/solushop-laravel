<?php

use Illuminate\Database\Seeder;

class AccountsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('accounts')->insert([
            [
                'account_id'            => '1',
                'account_description'   => 'General (Internal) / Cash Account',
                'created_at'            => \Carbon\Carbon::now(),
                'updated_at'            => \Carbon\Carbon::now(),
            ],

            [
                'account_id'            => '2',
                'account_description'   => 'General (External)',
                'created_at'            => \Carbon\Carbon::now(),
                'updated_at'            => \Carbon\Carbon::now(),
            ],

            [
                'account_id'            => '3',
                'account_description'   => 'Vendor (Internal)',
                'created_at'            => \Carbon\Carbon::now(),
                'updated_at'            => \Carbon\Carbon::now(),
            ],

            [
                'account_id'            => '4',
                'account_description'   => 'Vendor (External)',
                'created_at'            => \Carbon\Carbon::now(),
                'updated_at'            => \Carbon\Carbon::now(),
            ],

            [
                'account_id'            => '5',
                'account_description'   => 'Customer (Internal) / Solushop Wallet',
                'created_at'            => \Carbon\Carbon::now(),
                'updated_at'            => \Carbon\Carbon::now(),
            ],

            [
                'account_id'            => '6',
                'account_description'   => 'Customer (External)',
                'created_at'            => \Carbon\Carbon::now(),
                'updated_at'            => \Carbon\Carbon::now(),
            ],

            [
                'account_id'            => '7',
                'account_description'   => 'Sales Associate (Internal)',
                'created_at'            => \Carbon\Carbon::now(),
                'updated_at'            => \Carbon\Carbon::now(),
            ],

            [
                'account_id'            => '8',
                'account_description'   => 'Sales Associate (External)',
                'created_at'            => \Carbon\Carbon::now(),
                'updated_at'            => \Carbon\Carbon::now(),
            ],

            [
                'account_id'            => '9',
                'account_description'   => 'Delivery Partner (Internal)',
                'created_at'            => \Carbon\Carbon::now(),
                'updated_at'            => \Carbon\Carbon::now(),
            ],

            [
                'account_id'            => '10',
                'account_description'   => 'Delivery Partner (External)',
                'created_at'            => \Carbon\Carbon::now(),
                'updated_at'            => \Carbon\Carbon::now(),
            ],
        ]);
    }
}
