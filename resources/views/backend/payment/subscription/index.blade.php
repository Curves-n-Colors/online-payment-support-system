@extends('layouts.app')

@section('title', 'Subscription')

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
                        <div class="card-header">
                            <div class="card-title full-width">
                                <form action="{{ route('subscription.index') }}" method="GET">
                                    <div class="m-t-15 m-b-15">
                                        <div class="row">
                                            <label class="col-md-2 control-label overline"><strong>Filter By:</strong></label>
                                            <div class="col-md-10 form-group-attached">
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-3 col-lg-2 offset-lg-3">
                                                        <div class="form-group form-group-default no-padding">
                                                            <select name="type" data-init-plugin="select2"
                                                                class="full-width select-category form-control">
                                                                <option value="">Search by Type</option>
                                                                @php
                                                                $types = config('app.addons.recurring_type');
                                                                @endphp
                                                                @forelse ($types as $key => $type)
                                                                <option value="{{ $key }}" @if(request()->type == $key)
                                                                    selected
                                                                    @endif>{{
                                                                    $type }}</option>
                                                                @empty
                                                                @endforelse
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-12 col-md-3 col-lg-2">
                                                        <div class="form-group form-group-default no-padding">
                                                            <select name="category" data-init-plugin="select2"
                                                                class="full-width select-category form-control">
                                                                <option value="">Search by Category</option>
                                                                @forelse ($categories as $key => $category)
                                                                <option value="{{ $category->id }}" @if(request()->
                                                                    category ==
                                                                    $category->id) selected @endif>{{
                                                                    $category->name }}</option>
                                                                @empty
                                                                @endforelse
                                                            </select>
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
                                <h5 class="no-margin">Subscription List
                                    <a href="{{ route('subscription.create') }}"
                                        class="btn btn-info pull-right m-r-5">Create Subscription</a>
                                </h5>

                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table table-hover table-responsive-block dataTable with-export custom-table"
                                id="setup-table-list">
                                <thead>
                                    <tr>
                                        <th width="15">#</th>
                                        <th width="50">Subscripion Name</th>
                                        <th width="50">Client</th>
                                        <th width="50">Currency</th>
                                        <th width="50">Amount</th>
                                        <th width="50">Ref Date</th>
                                        <th width="50">Expiry Date</th>
                                        <th width="50">Type</th>
                                        <th width="50">Status</th>
                                        <th width="100">Option</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($data != null and count($data)>0)
                                    @php $i = 0; @endphp
                                    @foreach ( $data as $row )
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $row->details->title }}</td>
                                        <td>{{ $row->client->name }}<br />{{ $row->client->email }}</td>
                                        <td>{{ $row->details->currency }}</td>
                                        <td>{{ $row->details->total }}</td>
                                        <td>{{ $row->reference_date }}</td>
                                        <td>{{ $row->expire_date }}</td>

                                        <td>{{ isset($recurring_types[$row->details->recurring_type]) ?
                                            $recurring_types[$row->details->recurring_type] : 'N/A' }}</td>
                                        <td>
                                            @if($row->is_active==10)
                                            <strong class="text-success">ACTIVE</strong>
                                            @else
                                            <strong class="text-danger">INACTIVE</strong>
                                            @endif
                                        </td>
                                        <td class="list-item">
                                            <button class="btn btn-primary m-b-5 btn-view-more"
                                                type="button">VIEW</button>
                                            <a href="{{ route('subscription.edit', [$row->uuid]) }}"
                                                class="btn btn-info m-b-5">EDIT</a>
                                            {{-- <form action="{{ route('subscription.send', [$row->uuid]) }}"
                                                method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="entries" value="new">
                                                <button type="submit">SEND</button>
                                            </form> --}}

                                            <button class="btn btn-complete m-b-5 btn-proceed-init"
                                                data-url="{{ route('subscription.send', [$row->uuid]) }}" type="button">
                                                SEND
                                            </button>



                                            <input type="hidden" data-title="setup_title"
                                                value="{{ $row->details->title }}" class="payment-item">
                                            <input type="hidden" data-title="client" value='{{ $row->client->name }}'
                                                class="payment-item">
                                            <input type="hidden" data-title="email" value='{{ $row->client->email }}'
                                                class="payment-item">
                                            <input type="hidden" data-title="contact"
                                                value='{{ $row->client->phone_no }}' class="payment-item">
                                            <input type="hidden" data-title="total_amount"
                                                value="{{ $row->details->total . ' ' . number_format($row->total, 2) }}"
                                                class="payment-item">
                                            <input type="hidden" data-title="recurring_type"
                                                value='{{ $recurring_types[$row->details->recurring_type] }}'
                                                class="payment-item">
                                            <input type="hidden" data-title="reference_date"
                                                value='{{ $row->reference_date }}' class="payment-item">
                                            <input type="hidden" data-title="expire_date"
                                                value='{{ $row->expire_date }}' class="payment-item">

                                            <input type="hidden" data-title="payment_options"
                                                value='{{ $row->details->payment_options }}' class="payment-item">
                                            <input type="hidden" data-title="status"
                                                value='{{ $row->is_active == 10 ? "ACTIVE" : "INACTIVE" }}'
                                                class="payment-item">
                                            {{-- <input type="hidden" data-title="remarks" value='{{ $row->remarks }}'
                                                class="payment-item"> --}}
                                            <input type="hidden" data-title="created_at" value='{{ $row->created_at }}'
                                                class="payment-item">
                                            <input type="hidden" value='{{ $row->details->contents }}' class="contents">
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
@include('backend.payment.subscription.asset_index')
{{-- @include('backend.payment.setup.asset_index') --}}