@extends('layouts.plain')

@section('title', 'Login')

@section('content')
<div class="row full-width-fix">
    <div class="col-md-12">
        <h4 class="text-black-alt">SIGN IN TO YOUR ACCOUNT</h4>
        <form role="form" method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group form-group-default @error('email') has-error @enderror">
                <label>Login</label>
                <div class="controls">
                    <input type="email" class="form-control @error('email') error @enderror" name="email" placeholder="Username (email)" required autocomplete="off" value="{{ old('email') }}">
                    @error('email')
                        <label class="error">{{ $message }}</label>
                    @enderror
                </div>
            </div>
            <div class="form-group form-group-default @error('password') has-error @enderror">
                <label>Password</label>
                <div class="controls">
                    <input type="password" class="form-control @error('password') error @enderror" name="password" placeholder="Credentials" required autocomplete="off">
                    @error('password')
                        <label class="error">{{ $message }}</label>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6 visible-x">
                    @if (Route::has('password.request'))
                        <a class="normal btn btn-link text-black-alt" href="{{ route('password.request') }}">Lost your password?</a>
                    @endif
                </div>
                <div class="col-sm-12 col-md-6">
                    <button class="btn btn-warning btn-lg pull-right" type="submit">Sign in</button>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-12 hidden-x">
                    @if (Route::has('password.request'))
                        <a class="normal btn btn-link text-black-alt pull-right m-t-10" href="{{ route('password.request') }}">Lost your password?</a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
