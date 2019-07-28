<!--Header Middel Area Start-->
<div class="header-middel-area">
    <div class="container">
        <div class="row">
            <!--Logo Start-->
            <div class="col-md-2 col-sm-3 col-xs-12">
                <div class="logo">
                    <a href="{{ route('home') }}"><img src="{{ url('app/assets/img/logo/logo.png') }}" alt="Solushop Logo" style="width: 120px;
                        height: auto;"></a>
                </div>
            </div>
            <!--Logo End-->
            <!--Search Box Start-->
            <div class="col-md-7 col-sm-5 col-xs-12">
                <div class="search-box-area">
                    <form action="{{ route('show.shop.search') }}" method="POST">
                        @csrf
                        <div class="search-box">
                            <input type="text" name="search_query_string" id="search" placeholder="Search for something e.g. Perfumes" value=''>
                            <button name="Search" type="submit"><i class="ion-ios-search-strong"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <!--Search Box End-->
            <!--Mini Cart Start-->
            <?php
                //Selection Count or Favorites
                if (isset($customerID)) {
                    try {
                        $db                   = new db();
                        $db                   = $db->connect();
                        $selectCartItemsQuery = "SELECT * FROM cart WHERE  cart.User_ID = '$customerID'";
                        $stmt                 = $db->prepare($selectCartItemsQuery);
                        $stmt->execute();
                        $cartItemsResult = $stmt->fetchAll(PDO::FETCH_OBJ);
                    }
                    catch (PDOException $e) {
                        echo '{"error": {"text": ' . $e->getMessage() . '}';
                    }
                    if (is_null($cartItemsResult)) {
                        $cartCount = 0;
                    } else {
                        $cartCount = sizeof($cartItemsResult);
                    }
                } else {
                    $cartCount = 0;
                }
                if (isset($customerID)) {
                    //Selecting count favorites
                    try {
                        $db                       = new db();
                        $db                       = $db->connect();
                        $selectWishlistItemsQuery = "SELECT * FROM wishlist, products WHERE wishlist.Product_ID = products.ID AND wishlist.User_ID = '$customerID'";
                        $stmt                     = $db->prepare($selectWishlistItemsQuery);
                        $stmt->execute();
                        $wishlistItemsResult = $stmt->fetchAll(PDO::FETCH_OBJ);
                    }
                    catch (PDOException $e) {
                        echo '{"error": {"text": ' . $e->getMessage() . '}';
                    }
                    if (is_null($wishlistItemsResult)) {
                        $wishlistCount = 0;
                    } else {
                        $wishlistCount = sizeof($wishlistItemsResult);
                    }
                } else {
                    $wishlistCount = 0;
                }

                
            ?>
            <div class="col-md-3 col-sm-4 col-xs-12">
                <div class="mini-cart-area" style="text-align: center;">
                    <ul>
                        <li ><a href="wishlist.php"><i class="ion-android-star" style="color:white;"></i><span class="cart-add" style="color:white;"><?php echo $wishlistCount?></span></a></li>
                        <?php 
                            if ($cartCount < 1) {
                                echo "<li><a href=\"cart.php\"><i class=\"ion-android-cart\"></i><span class=\"cart-add\"> $cartCount</span></a></li>";
                            }else{
                                echo "<li><a href=\"cart.php\"><i class=\"ion-android-cart\"></i><span class=\"cart-add\"> $cartCount</span></a></li>";
                            }

                            
                        ?>
                    </ul>
                </div>
            </div>
            <!--Mini Cart End-->
        </div>
    </div>
</div>
<!--Header Middel Area End-->