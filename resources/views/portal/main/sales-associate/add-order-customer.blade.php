@extends('portal.layouts.sales-associate.master')

@section('page-title')Add Order, Select Customer @endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-md-8">
                <h5 class="card-title">Add Order - Step 1 (Select Customer)</h5>
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
                                        <th>Select</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @for($i=0; $i<sizeof($customers); $i++) 
                                        <tr>
                                            <td>{{ $customers[$i]["id"] }}</td>
                                            <td>{{ $customers[$i]["first_name"]." ".$customers[$i]["last_name"] }}</td>
                                            <td>{{ "0".substr($customers[$i]["phone"], 3) }}</td>
                                            <td>{{ $customers[$i]["email"] }}</td>
                                            <td>
                                                <button  
                                                    data-toggle="tooltip" 
                                                    data-popup="tooltip-custom" 
                                                    data-original-title="Initiate order for {{ $customers[$i]["first_name"]." ".$customers[$i]["last_name"] }}" 
                                                    class="btn btn-info btn-sm round customer"
                                                    customer-id   = '{{ $customers[$i]["id"] }}'
                                                    customer-name   = '{{ $customers[$i]["first_name"]." ".$customers[$i]["last_name"] }}'
                                                    customer-email   = '{{ $customers[$i]["email"] }}'
                                                    customer-phone   = '{{ "0".substr($customers[$i]["phone"], 3) }}'>
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
                <h5 class="card-title">Customer Details </h5>
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard" id="confirm-and-proceed">
                                Kindly select a customer from the list to your left. You may search by any part of the name, email, or phone. If customer does not exist, please add the customer from the add customer page.
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

        $( ".customer" ).click(function(){
            
            var customerID = $(this).attr('customer-id');
            var customerName = $(this).attr('customer-name');
            var customerEmail = $(this).attr('customer-email');
            var customerPhone = $(this).attr('customer-phone');

            var updateString = "<div class='row'><div class='col-md-12' style='padding-top: 10px;'><div class='row'><div class='col-md-1'></div><div class='col-md-4' style='font-weight: 600;'>ID : <br>Name : <br>Email : <br>Phone  : <br></div><div class='col-md-7'>"+customerID+" <br>"+customerName+" <br>"+customerEmail+" <br>"+customerPhone+" <br><br></div></div><div class='form-actions' style='text-align:center;'><a href='add/"+customerID+"'><button class='btn btn-success'><i class='la la-check' style='font-size: 13px;'></i> Proceed to Customer Addresses</button></a></div></div></div>";

            //populate modal inputs
            $('#confirm-and-proceed').html(updateString);

        });
    </script>
@endsection

