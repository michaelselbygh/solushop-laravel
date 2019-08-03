@extends('portal.layouts.manager.master')

@section('page-title')
    Subscriptions
@endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-12">
                <h4 class="card-title">Vendor Subscriptions</h4>
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered zero-configuration" id="subscriptions">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Vendor</th>
                                        <th>Vendor Phone</th>
                                        <th>Package</th>
                                        <th>Status</th>
                                        <th>Days Left</th>
                                        <th>Created On</th>
                                        <th>Last Updated</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @for($i=0; $i<sizeof($subscriptions); $i++) 
                                        <tr>
                                            <td>{{ $subscriptions[$i]->subscription_id }}</td>
                                            <td>{{ $subscriptions[$i]->name }}</td>
                                            <td>{{ "0".substr($subscriptions[$i]->phone, 3) }}</td>
                                            <td>{{ $subscriptions[$i]->vs_package_description }}</td>
                                            <td>
                                                @if ($subscriptions[$i]->vs_days_left > 0)
                                                    <span style="font-weight: 450; color: green">Active</span>
                                                @else
                                                    <span style="font-weight: 450; color: red">Expired</span>
                                                @endif
                                            </td>
                                            <td>{{ $subscriptions[$i]->vs_days_left }}</td>
                                            <td>{{ $subscriptions[$i]->subscription_created_at }}</td>
                                            <td>{{ $subscriptions[$i]->subscription_updated_at }}</td>
                                            <td>
                                                <a href="{{ url('portal/manager/vendor/'.$subscriptions[$i]->username) }}">
                                                    <button type="" style="margin-top: 3px; background-color: black !important; border-color: black !important" class="btn btn-success btn-sm round">
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
        $('#subscriptions').dataTable( {
            "order": [
                [4, 'asc'],
                [1, 'asc'],
                [5, 'asc']
            ]
        } );
    })
    </script>
@endsection

