@extends('layouts.app')

@section('title', 'Update Payment Setup')

@section('content')
@php 
$currency_codes = config('app.addons.currency_code'); 
$payment_options = config('app.addons.payment_options');
$recurring_types = config('app.addons.recurring_type'); 
$contents = ($data->contents != '') ? json_decode($data->contents) : '';
$payment_opts = ($data->payment_options != '') ? json_decode($data->payment_options, true) : '';
@endphp
<div class="container-fluid p-t-15 p-b-30">
    <form role="form" method="POST" action="{{ route('payment.setup.update', [$data->uuid]) }}">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-5 col-xlg-4">
                <h5>Update Payment Setup</h5>
                <div class="form-group form-group-default">
                    <label>Payment Setup Title</label>
                    <div class="controls">
                        <textarea class="form-control" name="title" placeholder="Payment Setup Title" style="height: 45px">{{ $data->title ?? old('title') }}</textarea>
                    </div>
                    @error('title')
                        <label class="error">{{ $message }}</label>
                    @enderror
                </div>
                <div class="form-group required form-group-default form-group-default-select2 @error('email') has-error @enderror">
                    <label>Client</label>
                    <select name="client" data-init-plugin="select2" class="full-width select-client form-control @error('email') error @enderror" data-placeholder="" required>
                        <option value="">Select a client</option>
                        @forelse ($clients as $key => $client)
                            <option value="{{ $client->id }}" data-email="{{ $client->email }}" @if($client->id == $data->client_id) selected @endif>{{ $client->name }}</option>
                        @empty
                        @endforelse
                    </select>
                    @error('client')
                        <label class="error">{{ $message }}</label>
                    @enderror
                </div>
                <div class="form-group required form-group-default @error('email') has-error @enderror">
                    <label>Email</label>
                    <div class="controls">
                        <input type="email" class="form-control email-client @error('email') error @enderror" name="email" placeholder="Email" required autocomplete="off" value="{{ $data->email ?? old('email') }}">
                        @error('email')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group required form-group-default input-group @error('currency') has-error @enderror">
                            <div class="form-input-group">
                                <label>Currency</label>
                                <select name="currency" class="full-width form-control select-currency @error('currency') error @enderror" required>
                                    @foreach ($currency_codes as $ccode)
                                    <option value="{{ $ccode }}" @if($ccode == $data->currency) selected @endif>{{ $ccode }}</option>
                                    @endforeach
                                </select>
                                @error('currency')
                                    <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>  
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group required form-group-default @error('total') has-error @enderror">
                            <label>Total Amount</label>
                            <div class="controls">
                                <input type="text" class="form-control total-amt @error('total') error @enderror" name="total" placeholder="Total Amount" required readonly autocomplete="off" value="{{ $data->total ?? old('total') }}" style="color: rgb(75 75 75);">
                                @error('total')
                                    <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>  
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group required form-group-default input-group @error('recurring_type') has-error @enderror">
                            <div class="form-input-group">
                                <label>Payment Recurrence Type</label>
                                <select name="recurring_type" class="full-width form-control select-recurrence @error('recurring_type') error @enderror" data-placeholder="" required>
                                    @foreach ($recurring_types as $key => $rctype)
                                    <option value="{{ $key }}" @if($key == $data->recurring_type) selected @endif>{{ $rctype }}</option>
                                    @endforeach    
                                </select>
                                @error('recurring_type')
                                    <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group required form-group-default @error('reference_date') has-error @enderror">
                            <label>Payment Reference Date</label>
                            <div class="controls">
                                <input type="text" class="form-control datepicker @error('reference_date') error @enderror" name="reference_date" placeholder="Expiry Date" required autocomplete="off" value="{{ $data->reference_date ?? old('reference_date') }}">
                                @error('reference_date')
                                    <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>  
                    </div>
                </div>
                <div class="form-group form-group-default">
                    <label>Remarks</label>
                    <div class="controls">
                        <textarea class="form-control" name="remarks" placeholder="Remarks" style="height: 90px">{{ $data->remarks ?? old('remarks') }}</textarea>
                    </div>
                </div>
                <!-- <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <div class="form-check info">
                            <input type="checkbox" name="is_advance" value="10" id="checkbox-advance" @if($data->is_advance == 10) checked @endif>
                            <label for="checkbox-advance">Advance Payment ?</label>
                        </div>
                    </div>
                </div> -->
                <div class="row" style="display:none;">
                    <div class="col-sm-12 col-md-12">
                        <div class="form-check info">
                            <input type="checkbox" value="10" id="checkbox-active" @if($data->is_active == 10) checked @endif>
                            <label for="checkbox-active">Active ?</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <h5>Payment Options</h5>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        @forelse ($payment_options as $pay_opt)
                        <div class="form-check danger">
                            <input type="checkbox" name="payment_options[]" value="{{ $pay_opt['code'] }}" id="payment-option-{{ $pay_opt['code'] }}" @if($payment_opts && in_array($pay_opt['code'], $payment_opts)) checked @endif class="payment-option @foreach($pay_opt['currency'] as $curr => $curr_id){{ $curr . ' '}}@endforeach" @if(!in_array($data->currency, array_flip($pay_opt['currency']))) disabled @endif>
                            <label for="payment-option-{{ $pay_opt['code'] }}">{{ $pay_opt['title'] }}</label>
                        </div>
                        @empty
                        <strong>No payment option available.</strong>
                        @endforelse
                        @error('payment_options')
                            <label class="error">{{ $message }}</label>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <button class="btn btn-complete m-t-15" type="submit">UPDATE PAYMENT SETUP</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <a href="{{ route('payment.setup.index') }}" class="btn btn-default m-t-10">GO BACK</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-md-12 col-lg-7 col-xlg-8">
                <div class="payment-container m-t-45">
                    @php $idx = 0; @endphp
                    @forelse ($contents as $content)
                    @php $idx++; @endphp
                    <div class="form-group-attached payment-content m-b-10">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group form-group-default">
                                    <div class="form-input-group">
                                        <label>Title</label>
                                        <input type="text" class="form-control" name="contents[{{$idx}}][title]" placeholder="Title" required value="{{ $content->title }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default input-group">
                                    <div class="form-input-group">
                                        <label>Amount</label>
                                        <input type="text" data-a-dec="." data-a-sep="," class="autonumeric form-control calc-amt" placeholder="Amount" name="contents[{{$idx}}][amount]" required value="{{ $content->amount }}">
                                    </div>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            @if ($idx==1)
                                            <button type="button" class="btn btn-complete btn-add">+</button>
                                            @else
                                            <button type="button" class="btn btn-danger btn-remove">X</button>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-default">
                            <label>Description</label>
                            <textarea name="contents[{{$idx}}][description]" class="form-control" placeholder="Description" style="height: 100px;">{{ $content->description }}</textarea>
                        </div>
                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group form-group-default">
                                    <label>Link Title</label>
                                    <input type="text" class="form-control" name="contents[{{$idx}}][link_title]" placeholder="Link Title" value="{{ $content->link_title }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-group-default">
                                    <label>Link URL</label>
                                    <input type="url" class="form-control" name="contents[{{$idx}}][link_url]" placeholder="Link URL" value="{{ $content->link_url }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="form-group-attached payment-content m-b-10">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group form-group-default">
                                    <div class="form-input-group">
                                        <label>Title</label>
                                        <input type="text" class="form-control" name="contents[1][title]" placeholder="Title" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group form-group-default input-group">
                                    <div class="form-input-group">
                                        <label>Amount</label>
                                        <input type="text" data-a-dec="." data-a-sep="," class="autonumeric form-control calc-amt" placeholder="Amount" name="contents[1][amount]" required>
                                    </div>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <button type="button" class="btn btn-complete btn-add">+</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group form-group-default">
                            <label>Description</label>
                            <textarea name="contents[1][description]" class="form-control" placeholder="Description" style="height: 67px;"></textarea>
                        </div>
                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group form-group-default">
                                    <label>Link Title</label>
                                    <input type="text" class="form-control" name="contents[1][link_title]" placeholder="Link Title">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-group-default">
                                    <label>Link URL</label>
                                    <input type="url" class="form-control" name="contents[1][link_url]" placeholder="Link URL">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforelse
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </form>
</div>
@endsection

@include('backend.payment.setup.asset_form')