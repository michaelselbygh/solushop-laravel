<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    
    {
        $this->call(CustomersTableSeeder::class);
        $this->call(DeliveryPartnersTableSeeder::class);
        $this->call(ManagersTableSeeder::class);
        $this->call(SalesAssociatesTableSeeder::class);
        $this->call(VendorsTableSeeder::class);
    }
}
