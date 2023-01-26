@extends('layouts.plain')

@section('title', 'Password Reset')

@section('content')
<div class="row full-width-fix">
    <div class="col-md-12">
        <h4 class="text-black-alt">RESET YOUR PASSWORD</h4>
        <form role="form" method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="form-group form-group-default @error('email') has-error @enderror">
                <label>Login</label>
                <div class="controls">
                    <input type="email" class="form-control @error('email') error @enderror" name="email" placeholder="Username (email)" required autocomplete="off" autofocus value="{{ $email ?? old('email') }}">
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
            <div class="form-group form-group-default @error('password_confirmation') has-error @enderror">
                <label>Confirm Password</label>
                <div class="controls">
                    <input type="password" class="form-control @error('password_confirmation') error @enderror" name="password_confirmation" placeholder="Credentials" required autocomplete="off">
                    @error('password_confirmation')
                        <label class="error">{{ $message }}</label>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6 visible-x">
                    <a class="normal btn btn-link text-black-alt" href="{{ route('login') }}">Cancel</a>
                </div>
                <div class="col-sm-12 col-md-6">
                    <button class="btn btn-warning btn-lg pull-right" type="submit">Reset Password</button>
                </div>
            </div>
            <div class="row hidden-x">
                <div class="col-sm-12 col-md-12">
                    <a class="normal btn btn-link text-black-alt pull-right m-t-10" href="{{ route('login') }}">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
