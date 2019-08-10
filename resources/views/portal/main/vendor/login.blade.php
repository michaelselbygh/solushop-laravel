@extends('portal.layouts.login.index')

@section('entity')Vendor @endsection

@section('login')
    <form class="form-horizontal form-simple" action="{{ route('vendor.login.submit') }}" method="POST" >
        @csrf
        <fieldset class="form-group position-relative has-icon-left mb-0">
            <input type="text" name="username" class="form-control form-control-lg input-lg" id="username" placeholder="Enter Your Username"
            value="{{ old('username') }}" required autocomplete="username" autofocus>
            <div class="form-control-position">
                <i class="ft-user"></i>
            </div>
        </fieldset>
        <fieldset class="form-group position-relative has-icon-left">
            <input type="password" name="password" class="form-control form-control-lg input-lg" id="password"
                placeholder="Enter Your Password" required autocomplete="current-password" autofocus>
            <div class="form-control-position">
                <i class="la la-key"></i>
            </div>
        </fieldset>
        <fieldset>
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="customCheck1" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="custom-control-label" for="customCheck1">Remember Me</label>
            </div>
        </fieldset>
        <div class="form-group row">
        </div>
        <button type="submit" name="login" class="btn btn-info btn-lg btn-block" style="background-color: #f68c20 !important; border-color: #f68c20 !important; border-radius: 10px;"><i class="ft-unlock"></i> Login</button>
    </form>
@endsection