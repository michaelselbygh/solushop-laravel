<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class SMSTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'sms';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_smsservicehelperqueue.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'sms_message',
			2 => 'sms_phone',
			3 => 'sms_state',
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
