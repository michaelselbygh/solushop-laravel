@extends('portal.layouts.manager.master')

@section('page-title')
   Add Vendor
@endsection

@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <h5 class="card-title">Add Vendor</h5>
        </div>
    </div>
    <form class="form" method="POST" action="{{ route("manager.process.add.vendor") }}" enctype="multipart/form-data">
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
                                            <label for="name">Name</label>
                                            <input id="name" name="name" class="form-control round" placeholder="Enter Vendor name" value="" type="text" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input id="email" name="email"  class="form-control round" placeholder="Enter email" value="" type="email" required> 
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="main_phone">Main Phone Number</label>
                                            <input id="main_phone" name="main_phone"  class="form-control round" placeholder="Enter main phone e.g. 0204456789" value="" type="text" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="alt_phone">Alternate Phone Number</label>
                                            <input id="alt_phone" name="alt_phone"  class="form-control round" placeholder="Enter alternate phone e.g. 0204456789" value="" type="text" required>
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
                                            <label for="pick_up_address">Address</label>
                                            <input id="pick_up_address" name="pick_up_address"  class="form-control round" placeholder="Enter Pick-Up Address" value="" type="text" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="header_image">Header Image</label>
                                            <input type="file" class="form-control-file" name="header_image" placeholder="Header Image" id="header_image" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions" style="text-align:center; padding: 20px;">
                                    <a href="{{ route("manager.show.add.vendor") }}">
                                        <button type="button" class="btn btn-danger mr-1" >
                                                Cancel
                                        </button>
                                    </a>
                                    <button type="submit" name="add_vendor" class="btn btn-success">
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

