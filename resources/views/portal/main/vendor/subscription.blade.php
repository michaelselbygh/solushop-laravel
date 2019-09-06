@extends('portal.layouts.vendor.master')

@section('page-title')Subscription @endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-md-4">
                @if (!isset($subscription["active"]))
                    <h5 class="card-title">Subscribe on Solushop</h5>
                    <div class="card">
                        <div class="card-content collapse show">
                            <div class="card-body card-dashboard"> 
                                <form method="POST" action="{{ route("vendor.process.subscription") }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="package">Select Package</label>
                                            <select class="form-control" name='package' id="package" style='border-radius:7px;' required>
                                                @for ($i = 0; $i < sizeof($subscription["options"]); $i++)
                                                    <option value="{{ $subscription["options"][$i]->id }}">
                                                        @if($subscription["options"][$i]->vs_package_cost == 0)
                                                            {{ $subscription["options"][$i]->vs_package_description }} - Free (Upload Limit : {{ $subscription["options"][$i]->vs_package_product_cap }} products.)
                                                        @else
                                                            {{ $subscription["options"][$i]->vs_package_description }} - GH¢ {{ $subscription["options"][$i]->vs_package_cost }} / month (Upload Limit : {{ $subscription["options"][$i]->vs_package_product_cap }} products.)
                                                        @endif
                                                        
                                                    </option>
                                                @endfor
                                            </select>
                                            <br>
                                            <div class="form-group">
                                                <label for="duration">Duration (In Months)</label>
                                                <input id="duration" name="duration" class="form-control round" placeholder="Enter duration" value="1" type="number" step="1" required>
                                            </div>
                                            <br>
                                            <div class="form-actions" style="text-align:center; padding: 0px;">
                                                <button type="submit" class="btn btn-success">
                                                    Subscribe
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>       
                @else
                    <h5 class="card-title">Your Subscription on Solushop</h5>
                    @include('portal.main.success-and-error.message')
                    <div class="card">
                        <div class="card-content collapse show">
                            <div class="card-body card-dashboard"> 
                                <table class="table table-hover table-xl mb-0">
                                    <thead>
                                        <tr>
                                            <td class="border-top-0">Description</td>
                                            <td class="border-top-0">Product Usage</td>
                                            <td class="border-top-0">Validity</td>
                                        </tr>
                                    </thead>
                                    <tbody> 
                                        <tr>
                                            <td>
                                                <b>{{ $subscription["active"]->package->vs_package_description }}</b>
                                            </td>
                                            <td>
                                                <b>{{ $subscription["active"]->product_count }}</b> / {{ $subscription["active"]->package->vs_package_product_cap }}
                                            </td>
                                            <td>
                                                @if($subscription["active"]->vs_days_left <= 5)
                                                    <b style="color:red">{{ $subscription["active"]->vs_days_left." ".str_plural('day', $subscription["active"]->vs_days_left) }}</b>
                                                @elseif($subscription["active"]->vs_days_left <= 15)
                                                    <b style="color:coral">{{ $subscription["active"]->vs_days_left." ".str_plural('day', $subscription["active"]->vs_days_left) }}</b>
                                                @else
                                                    <b style="color:green">{{ $subscription["active"]->vs_days_left." ".str_plural('day', $subscription["active"]->vs_days_left) }}</b>
                                                @endif
                                                left
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div> 

                    <h5 class="card-title">Upgrade or Extend</h5>
                    <div class="card">
                        <div class="card-content collapse show">
                            <div class="card-body card-dashboard"> 
                                <form method="POST" action="{{ route("vendor.process.subscription") }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="package">Select Package</label>
                                            <select class="form-control" name='package' id="package" style='border-radius:7px;' required>
                                                @for ($i = 0; $i < sizeof($subscription["options"]); $i++)
                                                    <option value="{{ $subscription["options"][$i]->id }}">
                                                        @if ($subscription["options"][$i]->id == $subscription["active"]->package->id)
                                                            @if($subscription["options"][$i]->vs_package_cost == 0)
                                                                Extend {{ $subscription["options"][$i]->vs_package_description }} Subscription - Free (Upload Limit : {{ $subscription["options"][$i]->vs_package_product_cap }} products.)
                                                            @else
                                                                Extend {{ $subscription["options"][$i]->vs_package_description }} Subscription - GH¢ {{ $subscription["options"][$i]->vs_package_cost }} / month (Upload Limit : {{ $subscription["options"][$i]->vs_package_product_cap }} products.)
                                                            @endif
                                                            
                                                        @elseif( $subscription["options"][$i]->id > $subscription["active"]->package->id)
                                                            @if($subscription["options"][$i]->vs_package_cost == 0)
                                                                Upgrade to {{ $subscription["options"][$i]->vs_package_description }} Subscription - Free (Upload Limit : {{ $subscription["options"][$i]->vs_package_product_cap }} products.)
                                                            @else
                                                                Upgrade to {{ $subscription["options"][$i]->vs_package_description }} Subscription - GH¢ {{ $subscription["options"][$i]->vs_package_cost }} / month (Upload Limit : {{ $subscription["options"][$i]->vs_package_product_cap }} products.)
                                                            @endif
                                                        @elseif($subscription["options"][$i]->id < $subscription["active"]->package->id)
                                                            @if($subscription["options"][$i]->vs_package_cost == 0)
                                                                Downgrade to {{ $subscription["options"][$i]->vs_package_description }} Subscription - Free (Upload Limit : {{ $subscription["options"][$i]->vs_package_product_cap }} products.)
                                                            @else
                                                                Downgrade to {{ $subscription["options"][$i]->vs_package_description }} Subscription - GH¢ {{ $subscription["options"][$i]->vs_package_cost }} / month (Upload Limit : {{ $subscription["options"][$i]->vs_package_product_cap }} products.)
                                                            @endif
                                                        @endif
                                                        
                                                    </option>
                                                @endfor
                                            </select>
                                            <br>
                                            <div class="form-group">
                                                <label for="duration">Duration (In Months)</label>
                                                <input id="duration" name="duration" class="form-control round" placeholder="Enter duration" value="1" type="number" step="1" required>
                                            </div>
                                            <br>
                                            <div class="form-actions" style="text-align:center; padding: 0px;">
                                                <button type="submit" class="btn btn-success">
                                                        Subscribe
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div> 
                @endif
            </div>
        </div>
    </section>
@endsection

