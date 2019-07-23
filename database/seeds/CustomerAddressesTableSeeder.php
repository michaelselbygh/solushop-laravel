<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class CustomerAddressesTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'customer_addresses';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_customer_address.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'ca_customer_id',
			2 => 'ca_region',
			3 => 'ca_town',
			4 => 'ca_address',
		];
	}

	public function run()
	{
		// Recommended when importing larger CSVs
		DB::disableQueryLog();

		// Uncomment the below to wipe the table clean before populating
		DB::table($this->table)->truncate();

		parent::run();
	}
}
