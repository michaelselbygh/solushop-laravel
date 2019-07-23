<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class ProductStatesTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'product_states';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_product_status.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'ps_description',
			2 => 'ps_html',
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
