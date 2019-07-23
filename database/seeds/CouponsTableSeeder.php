<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class CouponsTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'coupons';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_coupons.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'coupon_code',
			2 => 'coupon_value',
			3 => 'coupon_owner',
			4 => 'coupon_state',
			5 => 'coupon_expiry_date',
		];
	}

	public function run()
	{
		// Recommended when importing larger CSVs
		DB::disableQueryLog();

		// Uncomment the below to wipe the table clean before populating
		DB::table($this->table)->truncate();

		parent::run();


		//update states
		$coupons = App\Coupon::all();

        foreach ($coupons as $coupon) {
			DB::table('coupons')
			->where('id', $coupon->id)
			->update([
                'coupon_state' => $coupon->coupon_state + 1
            ]);
        }
	}
}
