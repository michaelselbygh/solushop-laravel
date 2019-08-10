@extends('portal.layouts.manager.master')

@section('page-title')Add Sales Associate @endsection

@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <h5 class="card-title">Add Sales Associate</h5>
        </div>
    </div>
    <form class="form" method="POST" action="{{ route("manager.process.add.sales.associate") }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-7">
                @include('portal.main.success-and-error.message')
                <div class="card" style="">
                    <div class="card-content collapse show">
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="first_name">First Name</label>
                                            <input id="first_name" name="first_name" class="form-control round" placeholder="Enter first name" value="" type="text" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name">Last Name</label>
                                            <input id="last_name" name="last_name"  class="form-control round" placeholder="Enter last name" value="" type="text" required> 
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input id="email" name="email"  class="form-control round" placeholder="Enter email" value="" type="email" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">Phone Number</label>
                                            <input id="phone" name="phone"  class="form-control round" placeholder="Enter phone e.g. 0204456789" value="" type="text" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="mode_of_payment">Mode of Payment</label>
                                            <fieldset class="form-group" >
                                                <select class="form-control" name='mode_of_payment' id="mode_of_payment" style='border-radius:7px;' required>
                                                    <option>MTN Mobile Money</option>
                                                    <option>Vodafone Cash</option>
                                                    <option>Bank Account</option>
                                                </select>
                                            </fieldset>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="payment_details">Payment Details</label>
                                            <input id="payment_details" name="payment_details"  class="form-control round" placeholder="Enter payment details" value="" type="text" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type_of_identification">Type Of Identification</label>
                                            <select class="form-control" name='type_of_identification' id="type_of_identification" style='border-radius:7px;' required>
                                                <option value='Voters ID'>Voters ID</option>
                                                <option value='Drivers License'>Drivers License</option>
                                                <option value='Passport'>Passport</option>
                                                <option value='Ghana Card'>Ghana Card</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="identification_file">Select Identification File</label>
                                            <input type="file" class="form-control-file" name="identification_file" placeholder="Identification File" id="identification_file" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="residential_address">Residential Address</label>
                                    <input id="residential_address" name="residential_address"  class="form-control round" placeholder="Enter Residential Address" value="" type="text" required>
                                </div>
                                <div class="form-actions" style="text-align:center; padding: 20px;">
                                    <a href="{{ route("manager.show.add.sales.associate") }}">
                                        <button type="button" class="btn btn-danger mr-1" >
                                                Cancel
                                        </button>
                                    </a>
                                    <button type="submit" name="add_sales_associate" class="btn btn-success">
                                            Save
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

