@extends('portal.layouts.manager.master')

@section('page-title')
   Generate Coupon
@endsection

@section('content-body')
    <div class="row">
        <div class="col-md-12">
            <h4 class="card-title">Generate Coupon</h4>
        </div>
    </div>
    <form class="form" method="POST" action="{{ route("manager.process.generate.coupon") }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-4">
                <div class="card" style="padding:10px;">
                    <h4 class="card-title" style="color:red">Note</h4>
                    <div class="card-content collapse show">
                        <div class="card-body" style="padding-top: 5px;">
                            <ol style="padding-left:0px;">
                                <li>Generating a coupon without Management concern is criminal and will result in immediate dismissal from Solushop. Criminal prosecution may be considered in addition.</li>
                                <li>Use this feature ONLY when you are instructed to do so.</li>
                                <li>All activity on Solushop Ghana is recorded.</li>
                            </ol>
                        </div>
                    </div>
                    </div>
                @include('portal.main.success-and-error.message')
                <div class="card" style="">
                    <div class="card-content collapse show">
                        <div class="card-body">
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="value">Value</label>
                                            <input id="value" name="value" class="form-control round" min="1" value="1" type="number" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="expiry_date">Expiry Date</label>
                                            <input id="expiry_date" name="expiry_date"  class="form-control round" value="" type="date" required> 
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions" style="text-align:center; padding: 20px;">
                                    <a href="{{ route("manager.show.coupons") }}">
                                        <button type="button" class="btn btn-danger mr-1" >
                                                Back To Coupons
                                        </button>
                                    </a>
                                    <button type="submit" name="generate_coupon" class="btn btn-success">
                                            Generate Coupon
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script>
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        var yyyy = today.getFullYear();
        if(dd<10){
                dd='0'+dd
            } 
            if(mm<10){
                mm='0'+mm
            } 

        today = yyyy+'-'+mm+'-'+dd;
        document.getElementById("expiry_date").setAttribute("min", today);
        document.getElementById("expiry_date").setAttribute("value", today);
    </script>
@endsection

