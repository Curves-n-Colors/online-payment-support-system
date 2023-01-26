@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="container-fluid p-t-15">
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-5 col-xlg-4">
            <h5>Edit Your Profile</h5>
            <form role="form" method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')
                <div class="form-group required form-group-default @error('name') has-error @enderror">
                    <label>Name</label>
                    <div class="controls">
                        <input type="text" class="form-control @error('name') error @enderror" name="name" placeholder="Name" required autocomplete="off" autofocus value="{{ $data->name ?? old('name') }}">
                        @error('name')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="form-group required form-group-default @error('email') has-error @enderror">
                    <label>Email</label>
                    <div class="controls">
                        <input type="email" class="form-control @error('email') error @enderror" name="email" placeholder="Email" required autocomplete="off" value="{{ $data->email ?? old('email') }}">
                        @error('email')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <button class="btn btn-complete" type="submit">UPDATE PROFILE</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <a href="{{ route('dash.index') }}" class="btn btn-default m-t-10">GO BACK</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection