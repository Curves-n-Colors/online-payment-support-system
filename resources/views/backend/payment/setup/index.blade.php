@extends('layouts.app')

@section('title', 'Payment Setups')

@section('content')
@php 
$recurring_types = config('app.addons.recurring_type'); 
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
	            <li class="active">
                    <a href="javascript:;" class="active">
                        Payment Setups
                    </a>
                </li>
	            <li class="">
                    <a href="{{ route('payment.entry.index') }}" class="">
                        Pending Payments
                    </a>
                </li>
                <li class="">
                    <a href="{{ route('payment.detail.index') }}" class="">
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
                                <h5 class="no-margin">Payment Setups
                                    <a href="{{ route('payment.setup.create') }}" class="btn btn-info pull-right m-r-5">Create Payment Setup</a>
                                </h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover table-responsive-block dataTable with-export custom-table" id="setup-table-list">
                                <thead>
                                    <tr>
                                        <th width="15">#</th>
                                        <th width="50">Setup Title</th>
                                        <th width="50">Client</th>
                                        <th width="50">Amount</th>
                                        <th width="50">Ref Date</th>
                                        <th width="50">Type</th>
                                        <th width="50">Status</th>
                                        <th width="100">Option</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($data != null)
                                        @php $i = 0; @endphp
                                        @foreach ($data as $row)
                                        @php $i++; $random = Illuminate\Support\Str::random(40). $i; @endphp
                                        <tr id="setup-{{$random}}" style="">
                                            <td>{{ $i }}</td>
                                            <td>{{ $row->title }}</td>
                                            <td>
                                                @if(count($row->clients)>0)
                                                    @foreach($row->clients as $data)
                                                        <span>
                                                        {{ $data->client->name }} <br>
                                                        {{ $data->client->email }} <br>
                                                        </span>    
                                                     @endforeach
                                                @else
                                                'N/A'
                                                @endif
                                            </td>
                                            {{-- <td>{{ $row->client->name }}<br/>{{ $row->client->email }}</td> --}}
                                            <td>{{ $row->currency . ' ' . number_format($row->total, 2) }}</td>
                                            <td>{{ $row->reference_date }}</td>
                                            <td>{{ isset($recurring_types[$row->recurring_type]) ? $recurring_types[$row->recurring_type] : 'N/A' }}</td>
                                            <td>
                                                @if ($row->is_active == 10)
                                                <strong class="text-success">ACTIVE</strong>
                                                @else
                                                <strong class="text-danger">INACTIVE</strong>
                                                @endif
                                            </td>
                                            <td class="list-item">
                                                <button class="btn btn-primary m-b-5 btn-view-more" type="button">VIEW</button>
                                                
                                                @if ($row->is_active == 10)
                                                <button class="btn btn-complete m-b-5 btn-proceed-init btn-get-entires" data-action="{{ route('payment.setup.entry', [$row->uuid]) }}" data-random="{{ $random }}" data-url="{{ route('payment.setup.send', [$row->uuid]) }}" type="button">SEND</button>
                                                <a href="{{ route('payment.setup.edit', [$row->uuid]) }}" class="btn btn-info m-b-5">EDIT</a>
                                                @endif
                                                {{-- BUTTON TO TEST SENN EMAIL --}}
                                                <form action="{{ route('payment.setup.send', [$row->uuid]) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <input type="hidden" name="entries" value="new">
                                                    <button type="submit">SEND</button>
                                                </form>

                                                <button class="btn {{ $row->is_active == 10 ? 'btn-danger' : 'btn-success' }} m-b-5 btn-change-status" type="button" data-index="{{ $i }}">
                                                    <span>{{ $row->is_active == 10 ? 'DEACTIVATE' : 'ACTIVATE' }}</span>
                                                </button>
                                                <form action="{{ route('payment.setup.change.status', [$row->uuid]) }}" method="POST" class="change-status-form-{{ $i }}" style="display: none;">@csrf @method('PUT')</form>

                                                <input type="hidden" data-title="setup_title" value="{{ $row->title }}" class="payment-item">
                                                <input type="hidden" data-title="client" value='{{ $row->client->name }}' class="payment-item">
                                                <input type="hidden" data-title="email" value='{{ $row->email }}' class="payment-item">
                                                <input type="hidden" data-title="total_amount" value="{{ $row->currency . ' ' . number_format($row->total, 2) }}" class="payment-item">
                                                <input type="hidden" data-title="recurring_type" value='{{ $recurring_types[1] }}' class="payment-item">
                                                <input type="hidden" data-title="reference_date" value='{{ $row->reference_date }}' class="payment-item">
                                                <input type="hidden" data-title="payment_timing" value='{{ $row->is_advance == 10 ? "ADVANCE PAYMENT" : "POST PAYMENT" }}' class="payment-item">
                                                <input type="hidden" data-title="payment_options" value='{{ $row->payment_options }}' class="payment-item">
                                                <input type="hidden" data-title="status" value='{{ $row->is_active == 10 ? "ACTIVE" : "INACTIVE" }}' class="payment-item">
                                                <input type="hidden" data-title="remarks" value='{{ $row->remarks }}' class="payment-item">
                                                <input type="hidden" data-title="created_by" value='{{ $row->user->name ?? "SYSTEM" }}' class="payment-item">
                                                <input type="hidden" data-title="created_at" value='{{ $row->created_at }}' class="payment-item">
                                                <input type="hidden" value='{{ $row->contents }}' class="contents">
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>

                            <form action="{{ route('payment.setup.index') }}" method="GET">
                                <div class="form-group-attached m-t-15">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-4 col-lg-4 offset-md-6 offset-lg-7">
                                            <div class="form-group form-group-default no-padding">
                                                <select name="client" data-init-plugin="select2" class="full-width select-client form-control">
                                                    <option value="">Filter by client</option>
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