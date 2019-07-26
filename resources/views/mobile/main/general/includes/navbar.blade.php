<!-- navbar home -->
<div class="navbar navbar-home">
    <div class="navbar-inner sliding">
        <div class="left">
            <a href="#" class="panel-open" data-panel="left"><i class="ti-align-left"></i></a>
        </div>
        <div class="title">
        <form class="searchbar" id="search-form" method="POST" action="{{ route('show.shop.search') }}">
            @csrf
                <div class="searchbar-input-wrap">
                    <input type="search" name="search_query_string" placeholder="Search e.g. Fashion">
                    <i class="searchbar-icon"></i>
                    <span class="input-clear-button"></span>
                </div>
                <span class="searchbar-disable-button">Cancel</span>
            </form>
        </div>
        <div class="right">
            <a href="/notification/" id="search-go-button" onclick="document.getElementById('search-form').submit();"><i class="ti-arrow-right"></i></a>
        </div>
    </div>
</div>
<!-- end navbar home -->