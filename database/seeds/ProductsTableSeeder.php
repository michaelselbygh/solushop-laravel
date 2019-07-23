<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class ProductsTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'products';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_products.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'product_cid',
			2 => 'product_name',
			12 => 'product_description',
			3 => 'product_features',
			4 => 'product_type',
			5 => 'product_settlement_price',
			6 => 'product_selling_price',
			7 => 'product_discount',
			8 => 'product_vid',
			10 => 'product_views',
			11 => 'product_tags',
			13 => 'product_dd',
			14 => 'product_dc',
			15 => 'product_state',
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
		$products = App\Product::all();

        foreach ($products as $product) {
			DB::table('products')
			->where('id', $product->id)
			->update([
				'product_state' => $product->product_state + 1,
				'product_slug' => str_slug($product->product_name, '-')
            ]);
        }

    }
}
