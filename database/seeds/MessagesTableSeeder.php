<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class MessagesTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'messages';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_message.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'message_sender',
			2 => 'message_content',
			3 => 'message_conversation_id',
			4 => 'message_timestamp',
			5 => 'message_read',
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
