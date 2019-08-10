@extends('portal.layouts.manager.master')

@section('page-title')Subscriptions @endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-12">
                <h5 class="card-title">Vendor Subscriptions</h5>
                @include('portal.main.success-and-error.message')
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
                                                    <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="View {{ $subscriptions[$i]->name }}"  style="margin-top: 3px;" class="btn btn-info btn-sm round">
                                                        <i class="ft-eye"></i>
                                                    </button>
                                                </a>
                                                @if ($subscriptions[$i]->vs_days_left > 0)
                                                    <button onclick="submitCancelSubscriptionForm('{{$subscriptions[$i]->subscription_id}}')" data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Cancel {{ $subscriptions[$i]->name }}'s Subscription" style="margin-top: 3px; background-color: red !important; border-color: red !important" class="btn btn-success btn-sm round">
                                                        <i class="ft-x"></i>
                                                    </button>
                                                @endif
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

    <form id="cancel-subscription-form" method="POST" action="{{ route("manager.process.subscriptions") }}">
        @csrf
        <input type="hidden" name="subscription_id" id="subscription_id"/>
    </form>

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

    function submitCancelSubscriptionForm(subscriptionID)
    {
        document.getElementById('subscription_id').value = subscriptionID;
        document.getElementById('cancel-subscription-form').submit(); 
    } 
    </script>
@endsection

