<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class CartItemsTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'cart_items';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_cart.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'ci_customer_id',
			2 => 'ci_sku',
			3 => 'ci_quantity',
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
