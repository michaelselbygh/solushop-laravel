<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class ConversationsTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'conversations';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_conversation.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'conv_key_structure',
			2 => 'conv_key',
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
