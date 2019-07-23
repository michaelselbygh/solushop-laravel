<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class OrderItemsStatesTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'order_items_states';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_order_items_state.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'ois_html',
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
