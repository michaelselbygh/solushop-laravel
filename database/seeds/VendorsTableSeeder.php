<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class VendorsTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'vendors';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_suppliers.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'name',
			2 => 'username',
			3 => 'phone',
			4 => 'alt_phone',
			5 => 'email',
			6 => 'address',
			7 => 'passcode',
			8 => 'balance',
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
		$vendors = App\Vendor::all();

        foreach ($vendors as $vendor) {
			DB::table('vendors')
			->where('id', $vendor->id)
			->update([
				'password' => bcrypt($vendor->passcode),
				'username' => str_slug($vendor->name, '-')
            ]);
        }
    }
}
