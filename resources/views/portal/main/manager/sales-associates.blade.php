@extends('portal.layouts.manager.master')

@section('page-title')
    Sales Associates
@endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-12">
                <h4 class="card-title">Sales Associates</h4>
                @include('portal.main.success-and-error.message')
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered zero-configuration" id="sales-associates">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>S-Coupon</th>
                                        <th>Payment Details</th>
                                        <th>Balance</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @for($i=0; $i<sizeof($sales_associates); $i++) 
                                        <tr>
                                            <td>{{ $sales_associates[$i]["id"] }}</td>
                                            <td>{{ $sales_associates[$i]["first_name"]." ".$sales_associates[$i]["last_name"] }}</td>
                                            <td>{{ "0".substr($sales_associates[$i]["phone"], 3) }}</td>
                                            <td>{{ $sales_associates[$i]["email"] }}</td>
                                            <td>{{ substr($sales_associates[$i]["id_file"], 0, 24) }}</td>
                                            <td>{{ $sales_associates[$i]["mode_of_payment"]." | ".$sales_associates[$i]["payment_details"] }}</td>
                                            <td>{{ $sales_associates[$i]["balance"] }}</td>
                                            <td>
                                                <a href="{{ route("manager.show.sales.associate", $sales_associates[$i]['id']) }}">
                                                    <button  data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="View {{ $sales_associates[$i]["first_name"]." ".$sales_associates[$i]["last_name"] }}" style="margin-top: 3px; background-color: black !important; border-color: black !important" class="btn btn-success btn-sm round">
                                                        <i class="ft-eye"></i>
                                                    </button>
                                                </a>
                                            </td>
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
        $('#sales-associates').dataTable( {
            "order": [
                [1, 'asc']
            ]
        } );
    })
    </script>
@endsection

