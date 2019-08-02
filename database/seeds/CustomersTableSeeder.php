<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class CustomersTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->insert_chunk_size = 500;
		$this->table = 'customers';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_customers.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'first_name',
			2 => 'last_name',
			3 => 'email',
			5 => 'phone',
			7 => 'activation_code',
			8 => 'old_password',
			9 => 'default_address',
			10 => 'date_of_birth',
			11 => 'milkshake',
			12 => 'icono',
			13 => 'sm',
			
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
