<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class ProductImagesTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'product_images';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_product_pictures.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'pi_product_id',
			2 => 'pi_path',
		];
	}

	public function run()
	{
		// Recommended when importing larger CSVs
		DB::disableQueryLog();

		// Uncomment the below to wipe the table clean before populating
		DB::table($this->table)->truncate();

        parent::run();
    }
}
