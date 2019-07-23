<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class OrderStatesTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'order_states';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_order_state.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'os_user_description',
			2 => 'os_user_html',
			3 => 'os_dp_html',
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
