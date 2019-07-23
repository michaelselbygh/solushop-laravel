<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class VSPackagesTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'vs_packages';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_vendor_subscription_package.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'vs_package_description',
			2 => 'vs_package_product_cap',
			3 => 'vs_package_days',
			4 => 'vs_package_cost',
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
