<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class VSPaymentsTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'vs_payments';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_vendor_subscription_payment.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'vs_payment_vendor_id',
			2 => 'vs_payment_vsp_id',
			3 => 'vs_payment_vspq',
			4 => 'vs_payment_amount',
			5 => 'vs_payment_token',
			6 => 'vs_payment_type',
			7 => 'vs_payment_state',
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
