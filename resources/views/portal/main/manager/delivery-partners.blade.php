@extends('portal.layouts.manager.master')

@section('page-title')
    Delivery Partners
@endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-12">
                <h5 class="card-title">Delivery Partners</h5>
                @include('portal.main.success-and-error.message')
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered zero-configuration" id="delivery-partners">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Company</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Passcode</th>
                                        <th>Payment Details</th>
                                        <th>Balance</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @for($i=0; $i<sizeof($delivery_partners); $i++) 
                                        <tr>
                                            <td>{{ $delivery_partners[$i]["id"] }}</td>
                                            <td>{{ $delivery_partners[$i]["dp_company"] }}</td>
                                            <td>{{ $delivery_partners[$i]["first_name"]." ".$delivery_partners[$i]["last_name"] }}</td>
                                            <td>{{ $delivery_partners[$i]["email"] }}</td>
                                            <td>{{ $delivery_partners[$i]["passcode"] }}</td>
                                            <td>{{ $delivery_partners[$i]["payment_details"] }}</td>
                                            <td>{{ $delivery_partners[$i]["balance"] }}</td>
                                            <td>
                                                <a href="{{ route("manager.show.delivery.partner", $delivery_partners[$i]['id']) }}">
                                                    <button  data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="View {{ $delivery_partners[$i]["first_name"]." ".$delivery_partners[$i]["last_name"] }}" style="margin-top: 3px; background-color: black !important; border-color: black !important" class="btn btn-success btn-sm round">
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
        $('#delivery-partners').dataTable( {
            "order": [
                [1, 'asc']
            ]
        } );
    })
    </script>
@endsection

