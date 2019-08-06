<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class OrdersTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'orders';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_orders.csv';
		$this->mapping = [
			0 => 'id',
			3 => 'order_type',
			1 => 'order_customer_id',
			4 => 'order_address_id',
			7 => 'order_subtotal',
			6 => 'order_shipping',
			8 => 'order_ad',
			9 => 'order_token',
			10 => 'order_scoupon',
			5 => 'order_state',
			2 => 'order_date',
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
		$orders = App\Order::all();

        foreach ($orders as $order) {
			DB::table('orders')
			->where('id', $order->id)
			->update([
				'order_state' => $order->order_state + 1,
				'dp_shipping' => 0
            ]);
        }
    }
}
