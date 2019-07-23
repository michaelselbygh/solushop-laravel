<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class StockKeepingUnitsTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'stock_keeping_units';
        $this->filename = base_path().'/database/seeds/csvs/solushop_table_stock_keeping_unit.csv';
        $this->mapping = [
			0 => 'id',
			1 => 'sku_product_id',
			2 => 'sku_variant_description',
			3 => 'sku_stock_left',
		];
	}

	public function run()
	{
		// Recommended when importing larger CSVs
		DB::disableQueryLog();

		// Uncomment the below to wipe the table clean before populating
		DB::table($this->table)->truncate();

        parent::run();

        //populate new fields
        $skus = App\StockKeepingUnit::all();

        foreach ($skus as $sku) {
            $product = App\Product::where('id', $sku->sku_product_id)->first();
            DB::table('stock_keeping_units')
            ->where('sku_product_id', $sku->sku_product_id)
            ->update([
                'sku_settlement_price' => $product->product_settlement_price,
                'sku_selling_price' => $product->product_selling_price,
                'sku_discount' => $product->product_discount,
            ]);
        }
    }
}
