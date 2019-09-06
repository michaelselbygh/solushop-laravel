@extends('portal.layouts.manager.master')

@section('page-title')Vendors @endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-md-12">
                <h5 class="card-title">Vendors</h5>
                @include('portal.main.success-and-error.message')
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered zero-configuration" id="vendors">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Username / Slug</th>
                                        <th>Main Phone</th>
                                        <th>Subscription</th>
                                        <th>Days Left</th>
                                        <th>Payment Details</th>
                                        <th>Balance</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @for($i=0; $i<sizeof($vendors); $i++) 
                                        <tr>
                                            <td>{{ $vendors[$i]["id"] }}</td>
                                            <td>{{ $vendors[$i]["name"] }}</td>
                                            <td>{{ $vendors[$i]["username"] }}</td>
                                            <td>{{ "0".substr($vendors[$i]["phone"], 3) }}</td>
                                            <td>
                                                {{ $vendors[$i]["subscription"]["package"]["vs_package_description"] }}
                                            </td>
                                            <td>
                                                {{ $vendors[$i]["subscription"]["vs_days_left"] }}
                                            </td>
                                            <td>{{ $vendors[$i]["mode_of_payment"]." | ".$vendors[$i]["payment_details"] }}</td>
                                            <td>{{ $vendors[$i]["balance"] }}</td>
                                            <td>
                                                <a href="{{ route("manager.show.vendor", $vendors[$i]['username']) }}">
                                                    <button  data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="View {{ $vendors[$i]["name"] }}" style="margin-top: 3px;" class="btn btn-info btn-sm round">
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
        $('#vendors').dataTable( {
            "order": [
                [5, 'desc'],
                [4, 'asc'],
                [1, 'asc']
            ]
        } );
    })
    </script>
@endsection

