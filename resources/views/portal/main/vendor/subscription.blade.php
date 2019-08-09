@extends('portal.layouts.vendor.master')

@section('page-title')
   Subscription
@endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-4">
                <h5 class="card-title">Your Subscription on Solushop</h5>
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard"> 
                            @if (is_null($subscription["active"]))
                                No
                            @else
                               yes 
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

