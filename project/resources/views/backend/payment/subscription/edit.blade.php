@extends('layouts.app')

@section('title', 'Create Subscription')

@section('content')

<div class="container-fluid p-t-15 p-b-30">
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
                                <select name="subscription_id" data-init-plugin="select2"
                                    class="full-width select-client form-control @error('subscription_id') error @enderror"
                                    data-placeholder="" required>
                                    @forelse ($subscription_plan as $key => $s)
                                    <option value="{{ $s->id }}" @if($subscription->payment_setup_id==$s->id) selected @endif>{{ $s->title }}
                                    </option>
                                    @empty
                                    <option>List no available</option>
                                    @endforelse
                                </select>
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
                                <select name="client" data-init-plugin="select2"
                                    class="full-width select-client form-control @error('client') error @enderror"
                                    data-placeholder="" required>
                                    @forelse ($clients as $key => $client)
                                    <option value="{{ $client->id }}" @if($subscription->client_id==$client->id) selected @endif>{{ $client->name
                                        }}
                                    </option>
                                    @empty
                                    <option>List no available</option>
                                    @endforelse
                                </select>
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
                                    value="{{ $subscription->reference_date }}">
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
                                    name="expire_date" placeholder="Expiry Date" autocomplete="off" value={{ $subscription->expire_date }}>
                                @error('expire_date')
                                <label class="error">{{ $message }}</label>
                                @enderror
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
                            <input type="checkbox" name="is_active" value="10" id="checkbox-active" {{ $subscription->is_active?'checked':''}}>
                            <label for="checkbox-active">Active ?</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <button class="btn btn-complete m-t-15 payment-create" type="submit">UPDATE
                            SUBSCRIPTION</button>

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

@section('page-specific-style')
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/select2.min.css') }}" />
<link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
@endsection

@section('page-specific-script')
<script src="{{ asset('assets/plugins/select2/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/plugins/jquery-autonumeric/autoNumeric.js') }}"></script>
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