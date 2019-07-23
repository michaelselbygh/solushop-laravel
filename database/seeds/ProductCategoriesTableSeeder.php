<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class ProductCategoriesTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'product_categories';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_product_categories.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'pc_parent',
			2 => 'pc_description',
			3 => 'pc_cna',
		];
	}

	public function run()
	{
		// Recommended when importing larger CSVs
		DB::disableQueryLog();

		// Uncomment the below to wipe the table clean before populating
		DB::table($this->table)->truncate();

		parent::run();
		
		//luge states
		$productCategories = App\ProductCategory::all();

        foreach ($productCategories as $productCategory) {
			//retrieving level
			if($productCategory->pc_parent == 0){
				$pc_level = 1;
			}elseif(in_array($productCategory->pc_parent, [1, 2, 3, 76, 93])){
				$pc_level = 2;
			}else{
				$pc_level = 3;
			}
			DB::table('product_categories')
			->where('id', $productCategory->id)
			->update([
				'pc_slug' => str_slug($productCategory->pc_description, '-'),
				'pc_level' => $pc_level
            ]);
        }
    }
}
