@extends('portal.layouts.sales-associate.master')

@section('page-title')Add Order, Select Address @endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-md-8">
                <h5 class="card-title">Add Order - Step 2 (Select {{$customer_addresses["customer"]["first_name"] }}'s Address)</h5>
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered zero-configuration" id="customer">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Town</th>
                                        <th>Address</th>
                                        <th>Select</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @for($i=0; $i<sizeof($customer_addresses["records"]); $i++) 
                                        <tr>
                                            <td>AD-{{ $customer_addresses["records"][$i]["id"] }}</td>
                                            <td>{{ $customer_addresses["records"][$i]["ca_town"] }}</td>
                                            <td>{{ $customer_addresses["records"][$i]["ca_address"] }}</td>
                                            <td>
                                                <button  
                                                    data-toggle="tooltip" 
                                                    data-popup="tooltip-custom" 
                                                    data-original-title="Select Address AD-{{ $customer_addresses["records"][$i]["id"] }}" 
                                                    class="btn btn-info btn-sm round address"
                                                    customer-id = "{{ $customer_addresses["customer"]["id"] }}"
                                                    address-id   = '{{ $customer_addresses["records"][$i]["id"] }}'
                                                    address-region   = '{{ $customer_addresses["records"][$i]["ca_region"] }}'
                                                    address-town   = '{{ $customer_addresses["records"][$i]["ca_town"] }}'
                                                    address-detail   = '{{ $customer_addresses["records"][$i]["ca_address"] }}'>
                                                    <i class="ft-check"></i>
                                                </button>
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
            <div class="col-md-4">
                <h5 class="card-title">Add Customer Address </h5>
                @include('portal.main.success-and-error.message')
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard" >
                            <form class="form" method="POST" action="{{ route("sales-associate.process.add.order.step-2", $customer_addresses["customer"]["id"]) }}" enctype="multipart/form-data">
                                <div class="form-body">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="address_town">Town</label>
                                            <select class="form-control" name='address_town' id="address_town" style='border-radius:7px; margin-bottom:15px;' required>
                                                @for ($i = 0; $i < sizeof($customer_addresses["options"]); $i++)
                                                    <option value="{{ $customer_addresses["options"][$i]["sf_town"] }}||{{ $customer_addresses["options"][$i]["sf_region"] }}">
                                                        {{ $customer_addresses["options"][$i]["sf_town"] }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label for="address_details">Address</label>
                                                <input id="address_details" name="address_details" class="form-control round" placeholder="Enter address" type="text" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions" style="text-align:center; padding: 0px;">
                                    <button type="submit" name="add_address" class="btn btn-success">
                                            Add Address
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <h5 class="card-title">Selected Address Details </h5>
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard" id="confirm-and-proceed">
                                Kindly select an address from the list to your left. You may search by any part of the town, or address. If customer does not have an address set up, please add one.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <script>
        $(document).ready(function(){
            $('#addresses').dataTable( {
                "order": [
                    [2, 'asc']
                ]
            } );
        })

        $( ".address" ).click(function(){
            var customerID = $(this).attr('customer-id');
            var addressID = $(this).attr('address-id');
            var addressRegion = $(this).attr('address-region');
            var AddressTown = $(this).attr('address-town');
            var addressDetail = $(this).attr('address-detail');

            var updateString = "<div class='row'><div class='col-md-12' style='padding-top: 10px;'><div class='row'><div class='col-md-1'></div><div class='col-md-4' style='font-weight: 600;'>ID : <br>Region : <br>Town : <br>Detail  : <br></div><div class='col-md-7'>AD-"+addressID+" <br>"+addressRegion+" <br>"+AddressTown+" <br>"+addressDetail+" <br><br></div></div><div class='form-actions' style='text-align:center;'><a href='"+customerID+"/"+addressID+"'><button class='btn btn-success'><i class='la la-check' style='font-size: 13px;'></i> Proceed to Select Products</button></a></div></div></div>";

            //populate modal inputs
            $('#confirm-and-proceed').html(updateString);

        });
    </script>
@endsection

