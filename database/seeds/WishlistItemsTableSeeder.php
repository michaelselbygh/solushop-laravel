<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class WishlistItemsTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'wishlist_items';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_wishlist.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'wi_customer_id',
			2 => 'wi_product_id',
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
