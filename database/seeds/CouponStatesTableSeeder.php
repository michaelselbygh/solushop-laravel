<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class CouponStatesTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'coupon_states';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_coupon_state.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'cs_state_description',
			2 => 'cs_state_html',
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
