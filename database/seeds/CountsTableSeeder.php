<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class CountsTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'counts';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_count.csv';
		$this->mapping = [
			0 => 'customer_count',
			1 => 'product_count',
			2 => 'order_count',
			3 => 'vendor_count',
			4 => 'sku_count',
			5 => 'coupon_count',
			6 => 'account',
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
