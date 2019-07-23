<?php

use Illuminate\Database\Seeder;
use Flynsarmy\CsvSeeder\CsvSeeder;

class ProductReviewsTableSeeder extends CsvSeeder
{
    public function __construct()
	{
		$this->table = 'product_reviews';
		$this->filename = base_path().'/database/seeds/csvs/solushop_table_product_reviews.csv';
		$this->mapping = [
			0 => 'id',
			1 => 'pr_customer_id',
			2 => 'pr_product_id',
			3 => 'pr_rating',
			4 => 'pr_comment',
			6 => 'pr_edited',
			5 => 'pr_date',
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
