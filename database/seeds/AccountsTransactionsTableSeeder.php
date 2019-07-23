<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class AccountsTransactionsTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'accounts_transactions';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_account_transactions.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'trans_type',
			2 => 'trans_amount',
			3 => 'trans_description',
			4 => 'trans_date',
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
