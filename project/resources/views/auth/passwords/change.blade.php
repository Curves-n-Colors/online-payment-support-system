@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
<div class="container-fluid p-t-15">
    <div class="row">
        <div class="col-sm-12 col-md-6 col-lg-5 col-xlg-4">
            <h5>Change Your Password</h5>
            <form role="form" method="POST" action="{{ route('password.changing') }}">
                @csrf
                <div class="form-group form-group-default @error('current_password') has-error @enderror">
                    <label>Current Password</label>
                    <div class="controls">
                        <input type="password" class="form-control @error('current_password') error @enderror" name="current_password" placeholder="Credentials" required autocomplete="off" autofocus>
                        @error('current_password')
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
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <button class="btn btn-info" type="submit">Update Password</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-sm-12 col-md-6 col-lg-5 col-xlg-4">
            <h5>Change Your Master Password</h5>
            <form role="form" method="POST" action="{{ route('password.master.changing') }}">
                @csrf
                <div class="form-group form-group-default @error('master_current_password') has-error @enderror">
                    <label>Current Password</label>
                    <div class="controls">
                        <input type="password" class="form-control @error('master_current_password') error @enderror" name="master_current_password" placeholder="Credentials" required autocomplete="off" autofocus>
                        @error('master_current_password')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="form-group form-group-default @error('master_password') has-error @enderror">
                    <label>Password</label>
                    <div class="controls">
                        <input type="password" class="form-control @error('master_password') error @enderror" name="master_password" placeholder="Credentials" required autocomplete="off">
                        @error('master_password')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="form-group form-group-default @error('master_password_confirmation') has-error @enderror">
                    <label>Confirm Password</label>
                    <div class="controls">
                        <input type="password" class="form-control @error('master_password_confirmation') error @enderror" name="master_password_confirmation" placeholder="Credentials" required autocomplete="off">
                        @error('master_password_confirmation')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <button class="btn btn-info" type="submit">Update Master Password</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection