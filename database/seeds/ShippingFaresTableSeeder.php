<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class ShippingFaresTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'shipping_fares';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_shipping_fares.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'sf_region',
			2 => 'sf_town',
			3 => 'sf_fare',
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
