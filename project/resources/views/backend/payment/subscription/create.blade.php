@extends('layouts.app')

@section('title', 'Create Subscription')

@section('content')

<div class="container-fluid p-t-15 p-b-30" x-data="{subscription: '', client: '', package: '',show_discount:false, continuous_discount:false,}">
    <form id="payment-setup-form" role="form" method="POST" action="{{ route('subscription.store') }}">
        @csrf
        <input type="hidden" id="setup_option" name="payment_option" value="0">

        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-6 col-xlg-6">
                <h5>Create Subscription</h5>
                <div class="row">
                    <div class="col-sm-12">
                        <div
                            class="form-group required form-group-default input-group @error('subscription_id') has-error @enderror">
                            <div class="form-input-group">
                                <label>Select Subscription plan</label>
                                <select name="subscription_id" id="subscription_id" x-ref="subscription" x-model="subscription"
                                    
                                    class="full-width select-client form-control @error('subscription_id') error @enderror"
                                    required>
                                    <option disabled selected>Select a Package</option>
                                    @forelse ($subscription as $key => $s)
                                    <option value="{{ $s->id }}">{{ $s->title }}
                                    </option>
                                    @empty
                                    <option>List no available</option>
                                    @endforelse
                                </select>
                                @error('subscription_id')
                                <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div
                            class="form-group required form-group-default input-group @error('client') has-error @enderror">
                            <div class=" form-input-group">
                                <label>Client</label>

                                <select  name="client_id[]" multiple x-ref="client" id="client_id"
                                    x-model="client" 
                                    class="full-width select-client form-control @error('client') error @enderror"
                                    required>
                                    @forelse ($clients as $key => $client)
                                    <option value="{{ $client->id }}" data-email="{{ $client->email }}">{{ $client->name
                                        }}
                                    </option>
                                    @empty
                                    <option>List no available</option>
                                    @endforelse
                                </select>
                                @error('client')
                                <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div
                            class="form-group required form-group-default @error('reference_date') has-error @enderror">
                            <label>Payment Reference Date</label>
                            <div class="controls">
                                <input type="text"
                                    class="form-control datepicker @error('reference_date') error @enderror"
                                    name="reference_date" placeholder="Start Date" required autocomplete="off"
                                    value="{{ date('Y-m-d') }}">
                                @error('reference_date')
                                <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group form-group-default @error('expire_date') has-error @enderror">
                            <label> Expire Date </label>
                            <div class="controls">
                                <input type="text" class="form-control datepicker @error('expire_date') error @enderror"
                                    name="expire_date" placeholder="Expiry Date" autocomplete="off">
                                @error('expire_date')
                                <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <div class="form-check info">
                            <input type="checkbox" name="has_discount" value="10" id="has_discount"
                                x-model="show_discount" autocomplete="off">
                            <label for="has_discount">Does the Subscription has discount ?</label>
                        </div>
                    </div>
                </div>
                <div class="row" x-show="show_discount" x-transition.delay.100ms>
                    <div class="col-6">
                        <div class="form-group form-group-default form-group-default-select2">
                            <label>Disount Type</label>
                            <select class=" full-width" data-init-plugin="select2"
                                data-placeholder="Select Discount Type" name="discount_type">
                                <option value="1">Rate(%)</option>
                                <option value="10">Amount</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group form-group-default">
                            <label> Discount Amount/Rate </label>
                            <div class="controls">
                                <input type="text" class="form-control" name="discount" placeholder="Discount" value="{{
                                    old('discount') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" x-show="show_discount" x-transition.delay.100ms>
                    <div class="col-sm-12 col-md-12">
                        <div class="form-check info">
                            <input type="checkbox" name="is_continuous_discount" value="1" id="continuous_discount"
                                autocomplete="off" x-model="continuous_discount">
                            <label for="continuous_discount">Apply Continuous Discount</label>
                        </div>
                    </div>
                </div>
                <div class="row" x-show="show_discount" x-transition.delay.100ms>
                    <div class="col-12">
                        <div class="form-group form-group-default">
                            <label> No of Discount in Each Payment </label>
                            <div class="controls">
                                <input type="number" class="form-control" name="no_disount"
                                    x-bind:disabled="continuous_discount"
                                    placeholder="No. of discount which can be applied in payment." value="{{
                                    old('no_disount')}}" min="1">
                            </div>
                        </div>
                    </div>
                </div>



                {{-- <div class="row">
                    <div class="col-6">
                        <div
                            class="form-group required form-group-default @error('no_of_payments') has-error @enderror">
                            <label>No. of Payments (Installments) </label>
                            <div class="controls">
                                <input type="number" class="form-control @error('no_of_payments') error @enderror"
                                    name="no_of_payments" placeholder="No of  Payment"
                                    value="{{ old('no_of_payments')}}" autocomplete="off">
                                @error('no_of_payments')
                                <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group required form-group-default @error('extended_days') has-error @enderror">
                            <label>No of Extended Days</label>
                            <div class="controls">
                                <input type="number" class="form-control  @error('extended_days') error @enderror"
                                    name="extended_days" placeholder="No Of  Extended Days" autocomplete="off"
                                    value="{{ old('extended_days') }}">
                                @error('extended_days')
                                <label class="error">{{ $message }}</label>
                                @enderror
                            </div>
                        </div>
                    </div>

                </div> --}}


                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <div class="form-check info">
                            <input type="checkbox" name="send_email" value="10" id="send_email" checked>
                            <label for="send_email">Do you want to send email right now ?</label>
                        </div>
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
                        <button class="btn btn-complete m-t-15 payment-create" type="submit">CREATE
                            SUBSCRIPTION</button>
                        {{-- <button class="btn btn-success m-t-15 btn-proceed-init create-and-send-payment"
                            type="button">CREATE
                            & SEND PAYMENT</button> --}}
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <a href="{{ route('subscription.index') }}" class="btn btn-default m-t-10">GO BACK</a>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

{{-- @include('backend.payment.setup.asset_index') --}}
@section('page-specific-style')
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/select2.min.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
@endsection

@section('page-specific-script')
<script src="{{ asset('assets/plugins/select2/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/plugins/jquery-autonumeric/autoNumeric.js') }}"></script>

<script>
    $('#client_id').select2({
        multiple: true,
    });
    $('#client_id').on('select2:select', (e) => {
        $subscription = $('#subscription_id').val();
        $client = e.params.data.id;
        sendRequest($subscription, $client);
    });

async function sendRequest(subscriptionId, clientId) {
    try {
    const response = await fetch(`validate?client_id=${clientId}&subscription_id=${subscriptionId}`, {
    method: 'GET',
    headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    }
    });
    const data = await response.json();
    let selectedValues = $('#client_id').val();
    
    if(data.code==405){
        notify_bar("danger", data.message);
        selectedValues = selectedValues.filter(value => value !== clientId);
        $('#client_id').val(selectedValues).trigger('change');
    }
   
    } catch (error) {
    console.error('Error:', error);
    }
    }
   
</script>
<script>
    $('[data-init-plugin=select2]').select2();
    $('.autonumeric').autoNumeric('init');
    $('.datepicker').datepicker({
    keyboardNavigation : false,
    forceParse : false,
    calendarWeeks : false,
    autoclose : true,
    format: 'yyyy-mm-dd',
    todayHighlight: true
    });
    
</script>

@endsection