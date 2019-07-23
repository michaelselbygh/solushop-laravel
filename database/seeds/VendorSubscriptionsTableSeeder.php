<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class VendorSubscriptionsTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'vendor_subscriptions';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_vendor_subscription.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'vs_vendor_id',
			2 => 'vs_vsp_id',
			3 => 'vs_days_left',
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
