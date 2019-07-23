<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class SMSStatesTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'sms_states';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_sms_status.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'sms_state_description',
			2 => 'sms_state_html',
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
