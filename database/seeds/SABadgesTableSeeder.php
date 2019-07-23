<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class SABadgesTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'sa_badges';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_steam_badges.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'sab_description',
			2 => 'sab_commission',
			3 => 'sab_image',
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
