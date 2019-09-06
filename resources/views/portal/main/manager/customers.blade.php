@extends('portal.layouts.manager.master')

@section('page-title')Customers @endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-md-12">
                <h5 class="card-title">Customers</h5>
                @include('portal.main.success-and-error.message')
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered zero-configuration" id="customer">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Balance</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @for($i=0; $i<sizeof($customers); $i++) 
                                        <tr>
                                            <td>{{ $customers[$i]["id"] }}</td>
                                            <td>{{ $customers[$i]["first_name"]." ".$customers[$i]["last_name"] }}</td>
                                            <td>{{ "0".substr($customers[$i]["phone"], 3) }}</td>
                                            <td>{{ $customers[$i]["email"] }}</td>
                                            <td>{{ abs(round(($customers[$i]['milk']['milk_value'] * $customers[$i]['milkshake']) - $customers[$i]['chocolate']['chocolate_value'] * 1, 2)) }}</td>
                                            <td>
                                                <a href="{{ route("manager.show.customer", $customers[$i]['id']) }}">
                                                    <button  data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="View {{ $customers[$i]["first_name"]." ".$customers[$i]["last_name"] }}" style="margin-top: 3px;" class="btn btn-info btn-sm round">
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
        $('#customer').dataTable( {
            "order": [
                [1, 'asc']
            ]
        } );
    })
    </script>
@endsection

