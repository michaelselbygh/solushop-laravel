@extends('app.layouts.general')
@section('page-title')Wishlist @endsection
@section('page-image'){{ url('app/assets/img/Solushop.jpg') }}@endsection
@section('page-description')Your wishlist on Solushop Ghana. @endsection
@section('page-content')
    <!--Heading Banner Area Start-->
    <section class="heading-banner-area pt-10">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="heading-banner">
                        <div class="breadcrumbs">
                            <ul>
                                <li><a href="{{ route('home') }}">Home</a><span class="breadcome-separator">></span></li>
                                <li>Wishlist</li>
                            </ul>
                        </div><br>
                    </div>
                    @include('app.main.general.success-and-error.message') 
                    <br>
                </div>
            </div>
        </div>
    </section>
    <!--Heading Banner Area End-->

    <section class="contact-form-area ">
        <div class="container">
            <div class="row">
                @if(!Auth::check())
                    <div class="col-sm-6 col-sm-offset-3"> 
                        <div class="search-form-wrapper mtb-70" style="text-align: center;">
                            <div class="error-message">
                                <p style="font-size: 16px;">
                                    Looks like you're not logged in.
                                    <br>
                                    Login below to access your wishlist.
                                </p>
                            </div>
                            <div class="search-form">
                                <div class="back-to-home">
                                    <a href="{{ route('login') }}">Login Here</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    @if(!isset($wishlist['wishlist_items']) OR sizeof($wishlist['wishlist_items']) < 1)
                        <div class="col-sm-6 col-sm-offset-3"> 
                            <div class="search-form-wrapper mtb-70" style="text-align: center;">
                                <div class="error-message">
                                    <p style="font-size: 16px;">
                                        Yikes, your wishlist is empty.
                                    </p>
                                </div>
                                <div class="search-form">
                                    <div class="back-to-home">
                                        <a href="{{ route('show.shop') }}">Start shopping</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                    <table style="width: 80%; margin:auto;">
                            <thead>
                                <tr>
                                    <th class="product-name">
                                        <span class="nobr"> Product</span>
                                    </th>
                                    <th class="product-price">
                                        <span class="nobr"> Unit Price </span>
                                    </th>
                                    <th class="product-total-price">
                                        <span class="nobr"> Availability </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < sizeof($wishlist['wishlist_items']); $i++)
                                    <tr >
                                        <td class="product-name" style='text-align:left;'>
                                            <a href="{{ url("shop/".$wishlist["wishlist_items"][$i]["vendor"]["username"]."/".$wishlist["wishlist_items"][$i]["product"]["product_slug"]) }}">
                                                <img src="{{ url("app/assets/img/products/thumbnails/".$wishlist["wishlist_items"][$i]["product_images"][0]["pi_path"].".jpg") }}" width='60px;' height='auto' style='border-radius:5px; margin-bottom: 10px;' alt="">
                                                &nbsp;&nbsp;
                                                {{ $wishlist["wishlist_items"][$i]["product"]["product_name"] }}
                                            </a>
                                            <span onclick="removeWishlistItem('{{ $wishlist['wishlist_items'][$i]['wi_product_id'] }}')" style="font-size: 22px; color: red; cursor: pointer;">×</span>
                                        </td>
                                        <td class="product-total-price">
                                            <span>GH¢ {{ $wishlist["wishlist_items"][$i]["product"]["product_selling_price"] - $wishlist["wishlist_items"][$i]["product"]["product_discount"] }}</span>
                                        </td>
                                        <td class="product-price">
                                            @if($wishlist["wishlist_items"][$i]["availability"] == 0)
                                                <span style="font-weight: 400; color: green;">Available</span>
                                            @else
                                                <span style="font-weight: 400; color: red;">Out of Stock</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endfor
                                <form id="remove-wishlist-item"  method="POST" action="{{ route("process.wishlist.action") }}">
                                    @csrf
                                    <input type="hidden" name="wishlist_action" value="remove_wishlist_item" />
                                    <input type="hidden" name="wishlist_item_id" id="wishlist_item_id" value="" />
                                </form>
                            </tbody>
                        </table>
                    @endif
                @endif
            </div>
            <br><br>
        </div>
    </section>

    <script>
        function removeWishlistItem(wishlistItemID)
        {
            document.getElementById('wishlist_item_id').value = wishlistItemID;
            document.getElementById('remove-wishlist-item').submit(); 
        } 
    </script>
@endsection