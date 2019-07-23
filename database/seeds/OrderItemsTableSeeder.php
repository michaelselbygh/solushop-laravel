<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class OrderItemsTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'order_items';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_order_items.csv';
		$this->mapping = [
			0 => 'oi_order_id',
			1 => 'oi_sku',
			2 => 'oi_name',
			4 => 'oi_selling_price',
			3 => 'oi_settlement_price',
			5 => 'oi_discount',
			6 => 'oi_quantity',
			7 => 'oi_state',
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
		$orderItems = App\OrderItem::all();

        foreach ($orderItems as $orderItem) {
			DB::table('order_items')
			->where('id', $orderItem->id)
			->update([
                'oi_state' => $orderItem->oi_state + 1
            ]);
        }
    }
}
