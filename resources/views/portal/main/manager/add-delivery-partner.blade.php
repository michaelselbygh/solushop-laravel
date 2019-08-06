@extends('portal.layouts.manager.master')

@section('page-title')
    Add Delivery Partner 
@endsection

@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <h5 class="card-title">Add Delivery Partner </h5>
            @include('portal.main.success-and-error.message')
        </div>
    </div>
   
        
    <div class="row">
        <div class="col-md-7">
            <div class="card" style="">
                <div class="card-content collapse show">
                    <div class="card-body">
                        <form class="form" method="POST" action="{{ route("manager.process.add.delivery.partner") }}" enctype="multipart/form-data">
                            <div class="form-body">
                                @csrf
                                <input type="hidden" name="sa_action" value="update_details"/>
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
                                            <label for="dp_company">Company</label>
                                            <input id="dp_company" name="dp_company"  class="form-control round" placeholder="Enter Company" type="text" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input id="email" name="email"  class="form-control round" placeholder="Enter email" value="" type="email" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="payment_details">Payment Details</label>
                                    <input id="payment_details" name="payment_details"  class="form-control round" placeholder="Enter Payment Details" value="" type="text" required>
                                </div>
                            </div>
                            <div class="form-actions" style="text-align:center; padding: 20px;">
                                <button type="submit" name="add_partner" class="btn btn-success">
                                        Add Delivery Partner
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

