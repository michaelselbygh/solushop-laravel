<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class DeliveryPartnersTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'delivery_partners';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_neglmanagement.csv';
		$this->mapping = [
			0 => 'id',
			5 => 'first_name',
			6 => 'last_name',
			3 => 'email',
			2 => 'passcode',
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
		$deliveryPartners = App\DeliveryPartner::all();

        foreach ($deliveryPartners as $deliveryPartner) {
			DB::table('delivery_partners')
			->where('id', $deliveryPartner->id)
			->update([
				'password' => bcrypt($deliveryPartner->passcode)
			]);
        }


	}
}
