<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class WTUPaymentsTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'wtu_payments';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_wtu_payments.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'wtu_payment_customer_id',
			2 => 'wtu_payment_wtup_id',
			3 => 'wtu_payment_token',
			4 => 'wtu_payment_status',
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
