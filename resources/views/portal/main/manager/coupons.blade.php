@extends('portal.layouts.manager.master')

@section('page-title')
    Coupons
@endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-12">
                <h4 class="card-title">Coupons</h4>
                @include('portal.main.success-and-error.message')
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered zero-configuration" id="coupons">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Code</th>
                                        <th>Value (GH¢)</th>
                                        <th>Owner</th>
                                        <th>Expiry Date</th>
                                        <th>State</th>
                                        <th>Created On</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @for($i=0; $i<sizeof($coupons); $i++) 
                                        <tr>
                                            <td>{{ $coupons[$i]["id"] }}</td>
                                            <td>{{ $coupons[$i]["coupon_code"] }}</td>
                                            <td>{{ $coupons[$i]["coupon_value"] }}</td>
                                            <td>{{ $coupons[$i]["coupon_owner"] }}</td>
                                            <td>
                                                @if ($coupons[$i]["coupon_expiry_date"] != "NA")
                                                    {{ date('l jS F Y', strtotime($coupons[$i]["coupon_expiry_date"])) }}
                                                @else
                                                   {{ $coupons[$i]["coupon_expiry_date"] }}
                                                @endif
                                            </td>
                                            <td>{!! $coupons[$i]["state"]["cs_state_html"] !!}</td>
                                            <td>{{ $coupons[$i]["created_at"] }}</td>
                                        </tr>
                                    @endfor
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <script>
    $(document).ready(function(){
        $('#coupons').dataTable( {
            "order": [
                [0, 'desc']
            ]
        } );
    })
    </script>
@endsection

