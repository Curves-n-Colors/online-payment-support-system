@extends('layouts.plain')

@section('title', 'Password Recovery')

@section('content')
<div class="row full-width-fix">
    <div class="col-md-12">
        <h4 class="text-black-alt">REQUEST FOR PASSWORD RESET</h4>
        <form role="form" method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group form-group-default @error('email') has-error @enderror">
                <label>Registered Email</label>
                <div class="controls">
                    <input type="email" class="form-control @error('email') error @enderror" name="email" placeholder="enter your registered email" required autocomplete="off" autofocus value="{{ old('email') }}">
                    @error('email')
                        <label class="error">{{ $message }}</label>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-6 visible-x">
                    <a class="normal btn btn-link text-black-alt" href="{{ route('login') }}">Cancel</a>
                </div>
                <div class="col-sm-12 col-md-6">
                    <button class="btn btn-warning btn-lg pull-right" type="submit">Send Password Reset Link</button>
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
