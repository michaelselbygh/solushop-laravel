<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    
    {
        $this->call(AccountsTableSeeder::class);
        $this->call(AccountsTransactionsTableSeeder::class);
        $this->call(CartItemsTableSeeder::class);
        $this->call(ChocolatesTableSeeder::class);
        $this->call(ConversationsTableSeeder::class);
        $this->call(CountsTableSeeder::class);
        $this->call(CouponsTableSeeder::class);
        $this->call(CouponStatesTableSeeder::class);
        $this->call(CustomerAddressesTableSeeder::class);
        $this->call(CustomersTableSeeder::class);
        $this->call(DeliveryPartnersTableSeeder::class);
        $this->call(ManagersTableSeeder::class);
        $this->call(MessagesTableSeeder::class);
        $this->call(MilkTableSeeder::class);
        $this->call(OrderItemsStatesTableSeeder::class);
        $this->call(OrderItemsTableSeeder::class);
        $this->call(OrdersTableSeeder::class);
        $this->call(OrderStatesTableSeeder::class);
        $this->call(ProductCategoriesTableSeeder::class);
        $this->call(ProductImagesTableSeeder::class);
        $this->call(ProductReviewsTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(ProductStatesTableSeeder::class);
        $this->call(SABadgesTableSeeder::class);
        $this->call(SalesAssociatesTableSeeder::class);
        $this->call(ShippingFaresTableSeeder::class);
        $this->call(SMSStatesTableSeeder::class);
        $this->call(SMSTableSeeder::class);
        $this->call(StockKeepingUnitsTableSeeder::class);
        $this->call(VendorsTableSeeder::class);
        $this->call(VendorSubscriptionsTableSeeder::class);
        $this->call(VSPackagesTableSeeder::class);
        $this->call(VSPaymentsTableSeeder::class);
        $this->call(WishlistItemsTableSeeder::class);
        $this->call(WTUPackagesTableSeeder::class);
        $this->call(WTUPaymentsTableSeeder::class);
    }
}
