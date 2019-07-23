<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class MilkTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'milk';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_milk.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'milk_value',
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
