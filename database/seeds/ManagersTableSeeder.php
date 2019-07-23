<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class ManagersTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'managers';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_manager.csv';
		$this->mapping = [
			0 => 'id',
			2 => 'first_name',
			3 => 'last_name',
			4 => 'email',
			5 => 'phone',
			7 => 'passcode',
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
		$managers = App\Manager::all();

        foreach ($managers as $manager) {
			DB::table('managers')
			->where('id', $manager->id)
			->update([
				'password' => bcrypt($manager->passcode)
			]);
        }
	}
}
