@extends('layouts.app')

@section('title', 'Payment Entry')

@section('content')
@php
$recurring_types = config('app.addons.recurring_type');
@endphp
<div class="container-fluid">
    <div class="row m-t-30">
        <div class="col-sm-12 col-md-12 col-lg-12">

            @include('backend.includes.nav')

            <div class="tab-content no-padding m-b-30">
                <div class="tab-pane slide-right active">
                    <div class="card m-b-0">
                        <div class="card-body">
                            <div class="card card-borderless">
                                <ul class="nav nav-tabs nav-tabs-simple" role="tablist"
                                    data-init-reponsive-tabs="dropdownfx">
                                    <li class="nav-item">
                                        <a class="{{ empty(request()->all())?'active':'' }}"
                                            href="{{ route('payment.entry.index') }}">All Payments</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="{{ isset(request()['pending'])?'active':'' }}"
                                            href="{{ route('payment.entry.index') }}?pending=">Pending
                                            Payments</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="{{ isset(request()['upcoming'])?'active':'' }}"
                                            href="{{ route('payment.entry.index') }}?upcoming=">Upcoming
                                            Payments</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="{{ isset(request()['expired'])?'active':'' }}"
                                            href="{{ route('payment.expired') }}">Expired Payments</a>
                                    </li>
                                </ul>

                            </div>
                            <form action="{{ route('payment.entry.index') }}" method="GET">
                                <div class="m-t-15 m-b-15">
                                    <div class="row">
                                        <label class="col-md-2 control-label overline"><strong>Filter
                                                By:</strong></label>
                                        <div class="col-md-10 form-group-attached">
                                            <div class="row">
                                                <div class="col-sm-12 col-md-3 col-lg-2 offset-lg-3">
                                                    <div class="form-group form-group-default">
                                                        <div class="controls">
                                                            <input type="text" class="form-control datepicker"
                                                                name="from" placeholder="From Date" autocomplete="off"
                                                                value="{{ request()->from }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-md-3 col-lg-2">
                                                    <div class="form-group form-group-default">
                                                        <div class="controls">
                                                            <input type="text" class="form-control datepicker" name="to"
                                                                placeholder="To Date" autocomplete="off"
                                                                value="{{ request()->to }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-md-4 col-lg-4">
                                                    <div class="form-group form-group-default no-padding">
                                                        <select name="client" data-init-plugin="select2"
                                                            class="full-width select-client form-control">
                                                            <option value="">Search by client</option>
                                                            @forelse ($clients as $key => $client)
                                                            <option value="{{ $client->uuid }}" @if(request()->
                                                                client ==
                                                                $client->uuid) selected @endif>{{
                                                                $client->name }}</option>
                                                            @empty
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-md-2 col-lg-1">
                                                    <button type="submit"
                                                        class="btn btn-lg btn-block btn-primary">FILTER</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <table class="table table-hover table-responsive-block dataTable with-export custom-table">
                                <thead>
                                    <tr>
                                        <th width="15">#</th>
                                        <th width="50">Entry Title</th>
                                        <th width="50">Setup Title</th>
                                        <th width="50">Client</th>
                                        <th width="50">Currency</th>
                                        <th width="50">Discount Amount</th>
                                        <th width="50">Amount</th>
                                        <th width="50">Payment Starting</th>
                                        <th width="50">Payment Ending</th>
                                        <th width="50">Status</th>
                                        <th width="100">Option</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- {{ dd($data) }} --}}
                                    @if ($data != null)
                                    @php $i = 0; @endphp
                                    @foreach ($data as $row)
                                    @php $i++; @endphp
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $row->title }}</td>
                                        <td>{{ $row->setup->title }}</td>
                                        <td>{{ $row->client->name }}<br />{{ $row->client->email }}
                                        </td>
                                        <td>{{ $row->currency }}</td>
                                        <td>
                                            {{ number_format($row->discount_amount, 2)}} <br>
                                        </td>
                                        <td>
                                            {{ number_format($row->total, 2)}} <br>
                                        </td>
                                        <td>{{ $row->start_date }}</td>
                                        <td>{{ $row->end_date }}</td>
                                        <td>
                                            @if ($row->is_expired == 10)
                                            <strong class="text-success">
                                                @if($row->is_extended == 10 && $row->is_active)
                                                EXTENDED
                                                @elseif($row->is_extended == 10 && $row->is_active == 0)
                                                SUSPENDED
                                                @else EXPIRED
                                                @endif
                                            </strong>
                                            @endif

                                            @if($row->is_completed)
                                            <br>
                                            <strong class="text-warning">COMPLETED</strong>
                                            @else
                                            <strong class="text-warning">PENDING</strong>
                                            @endif
                                        </td>
                                        <td class="list-item">
                                            <button class="btn btn-primary m-b-5 btn-view-more"
                                                type="button">VIEW</button>

                                            @if ($row->is_active == 10)
                                            <button class="btn btn-complete m-b-5 btn-proceed-init"
                                                data-url="{{ route('payment.entry.send', [$row->uuid]) }}"
                                                type="button">RESEND</button>
                                            {{-- BUTTON TO TEST SENN EMAIL --}}
                                            {{-- <form action="{{ route('payment.entry.send', [$row->uuid]) }}"
                                                method="POST">
                                                @csrf @method('PUT')
                                                <button type="submit">RESEND</button>
                                            </form> --}}
                                            <button class="btn btn-complete m-b-5 btn-proceed-init"
                                                data-url="{{ route('payment.entry.copy', [$row->uuid]) }}"
                                                type="button">COPY</button>
                                            @endif

                                            <button
                                                class="btn {{ $row->is_active == 10 ? 'btn-danger' : 'btn-success' }} m-b-5 btn-change-status"
                                                type="button" data-index="{{ $i }}">
                                                <span>{{ $row->is_active == 10 ? 'DEACTIVATE' :
                                                    'ACTIVATE' }}</span>
                                            </button>
                                            <form action="{{ route('payment.entry.change.status', [$row->uuid]) }}"
                                                method="POST" class="change-status-form-{{ $i }}"
                                                style="display: none;">@csrf @method('PUT')</form>

                                            <input type="hidden" data-title="entry_title" value="{{ $row->title }}"
                                                class="payment-item">
                                            <input type="hidden" data-title="setup_title"
                                                value="{{ $row->setup->title }}" class="payment-item">
                                            <input type="hidden" data-title="client" value='{{ $row->client->name }}'
                                                class="payment-item">
                                            <input type="hidden" data-title="email" value='{{ $row->email }}'
                                                class="payment-item">
                                            <input type="hidden" data-title="sub_amount" value="{{ $row->currency . ' ' . number_format($row->sub_total, 2) }}" class="payment-item">
                                            <input type="hidden" data-title="discount_amount" value="{{ $row->currency . ' ' . number_format($row->discount_amount, 2) }}" class="payment-item">
                                            <input type="hidden" data-title="vat_amount" value="{{ $row->currency . ' ' . number_format($row->vat, 2) }}" class="payment-item">
                                            <input type="hidden" data-title="total_amount" value="{{ $row->currency . ' ' . number_format($row->total, 2) }}" class="payment-item">
                                            <input type="hidden" data-title="payment_options"
                                                value='{{ $row->payment_options }}' class="payment-item">
                                            <input type="hidden" data-title="status"
                                                value='{{ $row->is_active == 10 ? "ACTIVE" : "INACTIVE" }}'
                                                class="payment-item">
                                            <input type="hidden" data-title="created_by"
                                                value='{{ $row->user->name ?? "SYSTEM" }}' class="payment-item">
                                            <input type="hidden" data-title="created_at" value='{{ $row->created_at }}'
                                                class="payment-item">
                                            <input type="hidden" value='{{ $row->contents }}' class="contents">
                                            <a href="{{ route('payment.entry.approve', [$row->uuid]) }}"
                                                class="btn btn-success">APPROVE PAYMENT</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@include('backend.payment.setup.asset_index')