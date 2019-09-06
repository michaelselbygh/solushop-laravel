@extends('portal.layouts.sales-associate.master')

@section('page-title')Add Customer @endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-md-5">
                <h5 class="card-title">Add Customer</h5>
                @include('portal.main.success-and-error.message')
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <form class="form" method="POST" action="{{ route("sales-associate.process.add.customer") }}" enctype="multipart/form-data">
                                <div class="form-body">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="first_name">First Name</label>
                                                <input id="first_name" name="first_name" class="form-control round" placeholder="Enter first name" value="{{ old('first_name') }}" type="text" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="last_name">Last Name</label>
                                                <input id="last_name" name="last_name"  class="form-control round" placeholder="Enter last name" value="{{ old('last_name') }}" type="text" required> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="phone">Phone</label>
                                                <input id="phone" name="phone"  class="form-control round" placeholder="Enter phone eg 054..." type="text" value="{{ old('phone') }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email">Email</label>
                                                <input id="email" name="email"  class="form-control round" placeholder="Enter email" value="{{ old('email') }}" type="email" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions" style="text-align:center; padding: 20px;">
                                    <button type="submit" name="add_customer" class="btn btn-success">
                                            Add Customer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

