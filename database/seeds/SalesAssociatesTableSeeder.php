<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class SalesAssociatesTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'sales_associates';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_steam.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'first_name',
			2 => 'last_name',
			3 => 'phone',
			4 => 'email',
			5 => 'passcode',
			6 => 'address',
			7 => 'badge',
			8 => 'id_type',
			9 => 'id_file',
			10 => 'mode_of_payment',
			11 => 'payment_details',
			12 => 'balance',
		];
	}

	public function run()
	{
		// Recommended when importing larger CSVs
		DB::disableQueryLog();

		// Uncomment the below to wipe the table clean before populating
		DB::table($this->table)->truncate();

        parent::run();

        //hashify
		$salesAssociates = App\SalesAssociate::all();

        foreach ($salesAssociates as $salesAssociate) {
			DB::table('sales_associates')
			->where('id', $salesAssociate->id)
			->update([
				'password' => bcrypt($salesAssociate->passcode),
				'badge' => $salesAssociate->badge + 1
			]);
        }
    }
}
