@extends('mobile.layouts.general')
@section('page-title')Wishlist @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Your wishlist on Solushop Ghana. @endsection
@section('page-content')
    <div class="page">
        @include('mobile.main.general.includes.toolbar')
        <div class="navbar navbar-page">
            <div class="navbar-inner sliding">
                <div class="left">
                    <a href="{{ URL::previous() }}" class="link back">
                        <i class="ti-arrow-left"></i>
                    </a>
                </div>
                <div class="title">
                    Wishlist
                </div>
            </div>
        </div>
        <div class="page-content">
            <!-- cart -->
            <div class="cart cart-page segments-page">
                <div class="container">
                    @if(!Auth::check())
                        <div class="content" style="text-align:center; margin: 0; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%);">
                            
                            <div class="error-message">
                                <h6>
                                    Looks like you're not logged in.
                                    <br>
                                    Login below to access your wishlist.
                                </h6>
                            </div>
                            <a href="{{ route("login") }}" class="external">
                                <button class="button" style="background-color:#f68b1e; width:100%">Login here</button>
                            </a>
                        </div>
                    @else
                        @if(!isset($wishlist['wishlist_items']) OR sizeof($wishlist['wishlist_items']) < 1)
                        <div class="content" style="text-align:center; margin: 0; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%);">
                            
                                <div class="error-message">
                                    <h6>
                                        Your wishlist is empty.
                                    </h6>
                                </div>
                                <a href="{{ route("show.shop") }}" class="external">
                                    <button class="button" style="background-color:#f68b1e; width:100%">Start Shopping here</button>
                                </a>
                            </div>
                        @else
                                @for($i = 0; $i < sizeof($wishlist['wishlist_items']); $i++)
                                    <div class="wrap-content">
                                        <div class="row">
                                            <div class="col-25">
                                                <div class="content-image">
                                                    <a href="{{ url("shop/".$wishlist["wishlist_items"][$i]["vendor"]["username"]."/".$wishlist["wishlist_items"][$i]["product"]["product_slug"]) }}" class="external">
                                                        <img src="{{ url("app/assets/img/products/thumbnails/".$wishlist["wishlist_items"][$i]["product_images"][0]["pi_path"].".jpg") }}" alt="">
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-55">
                                                <div class="content-text">
                                                    <a href="{{ url("shop/".$wishlist["wishlist_items"][$i]["vendor"]["username"]."/".$wishlist["wishlist_items"][$i]["product"]["product_slug"]) }}" class="external">
                                                        <p>
                                                            {{ $wishlist["wishlist_items"][$i]["product"]["product_name"] }}
                                                            <br>
                                                        </p>
                                                    </a>
                                                    <span onclick="removeWishlistItem('{{ $wishlist['wishlist_items'][$i]['wi_product_id'] }}')" style="font-size: 12px; color: red; cursor: pointer;">× Remove</span>
                                                </div>
                                            </div>
                                            <div class="col-20">
                                                <div class="content-info">
                                                    <span class="price">GH¢ {{ $wishlist["wishlist_items"][$i]["product"]["product_selling_price"] - $wishlist["wishlist_items"][$i]["product"]["product_discount"] }}</span>
                                                    @if($wishlist["wishlist_items"][$i]["availability"] == 0)
                                                        <span style="font-weight: 400; color: green;">Available</span>
                                                    @else
                                                        <span style="font-weight: 400; color: red;">Out of Stock</span>
                                                    @endif
                                                </div>
                                            </div>                        
                                        </div>
                                    </div>
                                    <!-- small divider -->
                                    <div class="small-divider"></div>
                                    <!-- end  small divider -->
                                @endfor
                            <form id="remove-wishlist-item"  method="POST" action="{{ route("process.wishlist.action") }}">
                                @csrf
                                <input type="hidden" name="wishlist_action" value="remove_wishlist_item" />
                                <input type="hidden" name="wishlist_item_id" id="wishlist_item_id" value="" />
                            </form>
                        @endif
                        <br><br>
                    @endif
                    

                </div>
            </div>
            @if (session()->has('error_message')) 
                <div id="snackbar">{{ session()->get('error_message') }}</div>
            @elseif (session()->has('success_message')) 
                <div id="snackbar">{{ session()->get('success_message') }}</div>
            @endif
            <!-- end cart -->
        </div>
    </div>
    <script>
        function removeWishlistItem(wishlistItemID)
        {
            document.getElementById('wishlist_item_id').value = wishlistItemID;
            document.getElementById('remove-wishlist-item').submit(); 
        } 
    </script>
    
@endsection    
    