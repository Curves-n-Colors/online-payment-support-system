@extends('layouts.app')

@section('title', 'Add Payment Method')

@section('content')
<div class="container-fluid p-t-15">
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-5 col-xlg-4">
            <h5>Add Payment Method</h5>
            <form role="form" method="POST" action="{{ route('payment.settings.store') }}">
                @csrf
                @php
                    $method = ['HBL', 'FONEPAY', 'ESEWA', 'KHALTI']
                @endphp
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group required form-group-default input-group @error('payment_method') has-error @enderror">
                            <div class="form-input-group">
                                <label>Select Subscription plan</label>
                                <select name="payment_method" data-init-plugin="select2"
                                    class="full-width select-client form-control @error('payment_method') error @enderror"
                                    data-placeholder="" required>
                                    @foreach ($method as $m )
                                        <option value="{{ $m }}">{{ $m }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group required form-group-default @error('public_key') has-error @enderror">
                    <label>Public Key</label>
                    <div class="controls">
                        <input type="text" class="form-control @error('public_key') error @enderror" name="public_key"
                            placeholder="Merchent Public Key" required autocomplete="off" value="{{ old('public_key') }}">
                        @error('public_key')
                        <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="form-group required form-group-default @error('secret_key') has-error @enderror">
                    <label>Secret Key</label>
                    <div class="controls">
                        <input type="text" class="form-control @error('secret_key') error @enderror" name="secret_key"
                            placeholder="Merchent Secret Key" required autocomplete="off" value="{{ old('secret_key') }}">
                        @error('secret_key')
                        <label class="error">{{ $message }}</label>
                        @enderror
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
                        <button class="btn btn-complete" type="submit">CREATE PAYMENT METHOD</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <a href="{{ route('payment.settings') }}" class="btn btn-default m-t-10">GO BACK</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection