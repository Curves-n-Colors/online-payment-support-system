@extends('layouts.app')

@section('title', 'Create Client')

@section('content')
<div class="container-fluid p-t-15">
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-5 col-xlg-4">
            <h5>Create Client</h5>
            <form role="form" method="POST" action="{{ route('client.store') }}">
                @csrf
                <div class="form-group required form-group-default @error('name') has-error @enderror">
                    <label>Name</label>
                    <div class="controls">
                        <input type="text" class="form-control @error('name') error @enderror" name="name" placeholder="Name" required autocomplete="off" autofocus value="{{ old('name') }}">
                        @error('name')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="form-group required form-group-default @error('email') has-error @enderror">
                    <label>Email</label>
                    <div class="controls">
                        <input type="email" class="form-control @error('email') error @enderror" name="email" placeholder="Email" required autocomplete="off" value="{{ old('email') }}">
                        @error('email')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="form-group form-group-default">
                    <label>Remarks</label>
                    <div class="controls">
                        <textarea class="form-control" name="remarks" placeholder="Remarks" style="height: 100px">{{ old('remarks') }}</textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <div class="form-check info">
                            <input type="checkbox" name="is_active" value="10" id="checkbox-active" checked>
                            <label for="checkbox-active">Active ?</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <button class="btn btn-complete" type="submit">CREATE CLIENT</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <a href="{{ route('client.index') }}" class="btn btn-default m-t-10">GO BACK</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection