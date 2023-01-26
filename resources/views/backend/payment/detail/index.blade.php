@extends('layouts.app')

@section('title', 'Payment History')

@section('content')
@php 
$payment_status = config('app.addons.payment_status');
$status_payment = config('app.addons.status_payment');
@endphp
<div class="container-fluid">
    <div class="row m-t-30">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <ul class="nav nav-tabs nav-tabs-fillup">
	            <li class="">
                    <a href="{{ route('client.index') }}" class="">
                        Clients
                    </a>
                </li>
	            <li class="">
                    <a href="{{ route('payment.setup.index') }}" class="">
                        Payment Setups
                    </a>
                </li>
	            <li class="">
                    <a href="{{ route('payment.entry.index') }}" class="">
                        Pending Payments
                    </a>
                </li>
                <li class="active">
                    <a href="javascript:;" class="active">
                        Payment History
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('email.index') }}" class="">
                        Email Notifications
                    </a>
                </li>
	        </ul>
	        <div class="tab-content no-padding m-b-30">
	            <div class="tab-pane slide-right active">
                    <div class="card m-b-0">
                        <div class="card-header">
                            <div class="card-title full-width">
                                <h5 class="no-margin">Payment History</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover table-responsive-block dataTable with-export custom-table">
                                <thead>
                                    <tr>
                                        <th width="15">#</th>
                                        <th width="50">Ref Code</th>
                                        <th width="50">Detail Title</th>
                                        <th width="50">Setup Title</th>
                                        <th width="50">Client</th>
                                        <th width="50">Amount</th>
                                        <th width="50">Payment Date</th>
                                        <th width="50">Paid Date</th>
                                        <th width="50">Status</th>
                                        <th width="100">Option</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($data != null)
                                        @php $i = 0; @endphp
                                        @foreach ($data as $row)
                                        @php $i++; @endphp
                                        <tr>
                                            <td>{{ $i }}</td>
                                            <td>{{ $row->ref_code }}</td>
                                            <td>{{ $row->title }}</td>
                                            <td>{{ $row->setup->title }}</td>
                                            <td>{{ $row->client->name }}<br/>{{ $row->email }}</td>
                                            <td>{{ $row->currency . ' ' . number_format($row->total, 2) }}</td>
                                            <td>{{ $row->payment_date }}</td>
                                            <td>{{ $row->created_at }}</td>
                                            <td>
                                                @if ($row->payment_type != null)
                                                    <strong class="text-complete">{{ $row->payment_type }}</strong>
                                                @endif
                                                @if ($row->payment_status != null)
                                                    <br/><strong class="{{ $row->payment_status == $status_payment['COMPLETED'] ? 'text-success' : 'text-danger' }}">{{ $payment_status[$row->payment_status] }}</strong>

                                                    @if ($row->payment_status == $status_payment['REFUNDED'])
                                                        <br/><strong class="text-danger">{{ $row->updated_at }}</strong>
                                                    @endif
                                                @else
                                                    <strong class="text-danger">N/A</strong>
                                                @endif
                                            </td>
                                            <td class="list-item">
                                                <button class="btn btn-primary m-b-5 btn-view-more" type="button">VIEW</button>
                                                @if ($row->payment_status == $status_payment['COMPLETED'])
                                                <a class="btn btn-info m-b-5" target="_blank" href="{{ route('payment.detail.invoice', $row->uuid) }}">GET INVOICE</a>
                                                @endif
                                                @if ($row->payment_type == 'NIBL' && $row->payment_status == $status_payment['COMPLETED'])
                                                <button class="btn btn-danger m-b-5 btn-refund" data-url="{{ route('payment.detail.refund', $row->uuid) }}" type="button">REFUND</button>
                                                @endif
                                                
                                                <input type="hidden" data-title="ref_code" value="{{ $row->ref_code }}" class="payment-item">
                                                <input type="hidden" data-title="detail_title" value="{{ $row->title }}" class="payment-item">
                                                <input type="hidden" data-title="setup_title" value="{{ $row->title }}" class="payment-item">
                                                <input type="hidden" data-title="client" value='{{ $row->client->name }}' class="payment-item">
                                                <input type="hidden" data-title="email" value='{{ $row->email }}' class="payment-item">
                                                <input type="hidden" data-title="currency" value="{{ $row->currency }}">
                                                <input type="hidden" data-title="total_amount" value="{{ $row->currency . ' ' . number_format($row->total, 2) }}" class="payment-item">
                                                <input type="hidden" data-title="payment_date" value='{{ $row->payment_date }}' class="payment-item">
                                                <input type="hidden" data-title="paid_date" value='{{ $row->created_at }}' class="payment-item">
                                                <input type="hidden" data-title="status" value='{{ $payment_status[$row->payment_status] ?? "N/A" }}' class="payment-item">
                                                @if ($row->payment_status == $status_payment['REFUNDED'])
                                                <input type="hidden" data-title="refund_date" value='{{ $row->updated_at }}' class="payment-item">
                                                @endif
                                                <input type="hidden" value='{{ $row->contents }}' class="contents">

                                                @if ($row->payment_type == 'NIBL')
                                                    @php 
                                                    $transaction = collect($row->payment_nibl)->filter(function ($value, $key) {
                                                        return !in_array($key, ['id', 'uuid', 'created_at', 'updated_at']);
                                                    });
                                                @endphp
                                                @elseif ($row->payment_type == 'KHALTI')
                                                    @php 
                                                    $transaction = collect($row->payment_khalti)->filter(function ($value, $key) {
                                                        return !in_array($key, ['id', 'uuid', 'created_at', 'updated_at']);
                                                    });
                                                    @endphp
                                                @else
                                                    @php $transaction = []; @endphp
                                                @endif
                                                
                                                <input type="hidden" value='{{ json_encode($transaction) }}' class="transactions">
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>

                            <form action="{{ route('payment.detail.index') }}" method="GET">
                                <div class="form-group-attached m-t-15">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-3 col-lg-2 offset-lg-3">
                                            <div class="form-group form-group-default">
                                                <div class="controls">
                                                    <input type="text" class="form-control datepicker" name="from" placeholder="From Date" autocomplete="off" value="{{ request()->from }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-3 col-lg-2">
                                            <div class="form-group form-group-default">
                                                <div class="controls">
                                                    <input type="text" class="form-control datepicker" name="to" placeholder="To Date" autocomplete="off" value="{{ request()->to }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-4 col-lg-4">
                                            <div class="form-group form-group-default no-padding">
                                                <select name="client" data-init-plugin="select2" class="full-width select-client form-control">
                                                    <option value="">Search by client</option>
                                                    @forelse ($clients as $key => $client)
                                                        <option value="{{ $client->uuid }}" @if(request()->client == $client->uuid) selected @endif>{{ $client->name }}</option>
                                                    @empty
                                                    @endforelse
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-2 col-lg-1">
                                            <button type="submit" class="btn btn-lg btn-block btn-primary">SEARCH</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@include('backend.payment.setup.asset_index')