@extends('layouts.app')

@section('title', 'Create Payment Setup')

@section('content')
@php
$currency_codes = config('app.addons.currency_code');
$payment_options = config('app.addons.payment_options');
$recurring_types = config('app.addons.recurring_type');
$contents = ($entry->contents != '') ? json_decode($entry->contents) : '';
@endphp
<style>
    .white-bg{
        background-color: #fff;
    }
    .bg-contrast-low {
    background-color: #f4f4f4 !important;
    }
</style>
<div class="container-fluid p-t-15 p-b-30">
   <div class="row row-same-height white-bg" >
    <div class="col-md-8 b-r b-dashed b-grey ">
        <div class="padding-30 sm-padding-5 sm-m-t-15 m-t-50">
            <h2 class="title">{{ $entry->title }}</h2>
            <p>Payment Details for <strong>{{ $entry->subscription->client->name  }}</strong></p>
            <table class="table table-condensed">
                @forelse ($contents as $content)
                <tr>
                    <td class=" col-md-9">
                        <span class="m-l-10 font-montserrat fs-11 all-caps">{{ $content->title }}</span>
                        <span class="m-l-10 ">{{ $content->description }}</span>
                    </td>
                    <td class="col-md-3 text-right">
                        <span>{{ $entry->currency }} {{ $content->amount }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <span class="m-l-10 font-montserrat fs-11 all-caps">No details available.</span>
                </tr>
                @endforelse

                <tr class="advance_payment">
                    <td class=" col-md-9">
                        <span class="m-l-10 font-montserrat fs-11 all-caps">Advance Payment</span>
                    </td>
                    <td class="col-md-3 text-right">
                        <span>x <span id="no_months">2</span> MONTHS</span></span>
                    </td>
                </tr>

                <tr>
                    <td colspan="2" class=" col-md-3 text-right">
                        <h4 class="text-primary no-margin font-montserrat">{{ $entry->currency }} <span id="amount">{{ number_format($entry->total, 2) }}</span></h4>
                    </td>
                </tr>
            </table>
            {{-- <p class="small">Invoice are issued on the date of despatch. Payment terms: Pre-orders: within 10 days of
                invoice date with 4% discount, from the 11th to the 30th day net. Re-orders: non-reduced stock items are
                payable net after 20 days. </p>
            <p class="small">By pressing Pay Now You will Agree to the Payment <a href="#">Terms &amp; Conditions</a>
            </p> --}}
        </div>
    </div>
    <div class="col-md-4">
        <div class="padding-30 sm-padding-5">
            <form role="form" action="{{ route('payment.entry.approve.submit',[$entry->uuid]) }}" method="POST">
                @csrf
                <div class="bg-contrast-low padding-30 b-rad-lg">
                    <h4>Payment Options</h4>
                    <p class="m-t-10 m-b-10">Select the method to approve the payment.</p>
                    <div class="col-lg-5 col-md-6 m-b-20">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label class="btn btn-default active">
                                <input type="radio" name="payment_type" id="option1" checked value="Cash"> <span class="fs-16">Cash</span>
                            </label>
                            <label class="btn btn-default">
                                <input type="radio" name="payment_type" id="option2" value="Cheque"> <span class="fs-16">Cheque</span>
                            </label>
                        </div>
                    </div>
                    @if(isset($diff) and $diff>1)
                    <div class="advance">
                        <div class="form-check complete">
                            <input type="checkbox" id="is_advance" name="is_advance" value="1" autocomplete="off">
                            <label for="is_advance">Do you want to advance pay?</label>
                        </div>

                        <p>Select no. of months for the advance pay. </p>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group required form-group-default input-group" id="advance_month">
                                    <div class="form-input-group">
                                        <select name="selected_month" data-init-plugin="select2" class="full-width select-client form-control" id="selected_month">
                                           @for($i=2; $i<=$diff; $i++)
                                           <option value={{ $i }}>{{ $i.' MONTHS' }}</option>
                                           @endfor
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <button class="btn btn-success m-t-15" type="submit">APPROVE</button>
                            {{-- <button class="btn btn-success m-t-15 btn-proceed-init create-and-send-payment" type="button">CREATE
                                & SEND PAYMENT</button> --}}
                        </div>
                    </div>

                    <div class="clearfix"></div>
                
                </div>
            </form>
        </div>
    </div>
</div>
</div>

@endsection
@section('page-specific-script')
<script>
    var amount = {!!json_encode(($entry->total)) !!};
    var title = {!!json_encode($entry->title) !!};
    var subTitle = title.indexOf('(');
    
    var paymentTitle = title.substring(0, subTitle)
    
    var startDate = {!!json_encode($entry->start_date) !!};

    $(document).ready(function() {
        $('#advance_month').hide();
        $('.advance_payment').hide();
        $('#is_advance').click(function(){
            if($(this).is(':checked')){
                $('#advance_month').show();
            }else{
                $('#advance_month').hide();
                $('.advance_payment').hide();
                $('#amount').text(amount);
                $('h2.title').text(title);
        }
        });
    });

    $('#selected_month').change(function(){
        var value = $(this).val();
        var advanceAmount = parseFloat(value * parseFloat(amount) ).toFixed(2);
        console.log(amount);
        if(value>0){
            $('.advance_payment').show();
            $("#no_months").text(value);
            $('#amount').text(advanceAmount);
            //ADD DATE IN STARTING DATE ONLY WHEN MONTH IS SELECTED
            const date = new Date(startDate);
            date.setMonth(date.getMonth() + parseInt(value));
            var endDate=date.toISOString().slice(0, 10);
            var newTitle = `${paymentTitle} ( ${startDate} TO ${endDate})`;
            $('h2.title').text(newTitle);
            console.log(newTitle);
            }else{
                $('.advance_payment').hide();
            }
            console.log(advanceAmount);
        });
</script>
@endsection
{{-- @include('backend.payment.setup.asset_index') --}}
@include('backend.payment.setup.asset_form')