<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class ChocolatesTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'chocolates';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_chocolate.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'chocolate_value',
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
