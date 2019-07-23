<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class WTUPackagesTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'wtu_packages';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_wallet_topup_packages.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'wtu_package_description',
			2 => 'wtu_package_cost',
			3 => 'wtu_package_bonus',
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
