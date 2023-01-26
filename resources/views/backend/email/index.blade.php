@extends('layouts.app')

@section('title', 'Email Notifications')

@section('content')
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
                <li class="">
                    <a href="{{ route('payment.detail.index') }}" class="">
                        Payment History
                    </a>
                </li>
                <li class="active">
                    <a href="javascript:;" class="active">
                        Email Notifications
                    </a>
                </li>
	        </ul>
	        <div class="tab-content no-padding m-b-30">
	            <div class="tab-pane slide-right active">
                    <div class="card m-b-0">
                        <div class="card-body">
                            <table class="table dataTable with-no-export custom-table">
                                <thead>
                                    <tr>
                                        <th style="padding-left: 0 !important; font-size: 13px;"><strong>Email Notification Logs</strong></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($data != null)
                                        @foreach ($data as $row)
                                        @php $listing = json_decode($row->data, true); @endphp
                                        <tr>
                                            <td class="no-padding">
                                                <span class="pre-fake m-t-15">
                                                    <ul>
                                                        <li>{</li>
                                                        @if ($row->payment_entry)
                                                        <li class="p-l-25">"setup_title" : "{{ $row->payment_entry->setup->title }}",</li>
                                                        <li class="p-l-25">"entry_title" : "{{ $row->payment_entry->title }}",</li>
                                                        @endif
                                                        @if ($row->payment_detail)
                                                        <li class="p-l-25">"setup_title" : "{{ $row->payment_detail->setup->title }}",</li>
                                                        <li class="p-l-25">"detail_title" : "{{ $row->payment_detail->title }}",</li>
                                                        @endif

                                                        <li class="p-l-25">"client" : "{{ $row->client->name }}",</li>
                                                        <li class="p-l-25">"email" : "{{ $row->email }}",</li>
                                                        
                                                        @foreach ($listing as $key => $item)
                                                        @if($key == 'link')
                                                        <li class="p-l-25">"{{ $key }}" : "<span data-copy="{{ $item }}" data-title="Link" class="btn-copy-to-clipboard text-danger cursor-pointer">click here to copy the link</span>",</li>
                                                        @else
                                                        <li class="p-l-25">"{{ $key }}" : "{{ $item }}",</li>
                                                        @endif
                                                        @endforeach
                                                        
                                                        <li class="p-l-25">"sent_at" : "{{ $row->created_at }}"</li>
                                                        <li>}</li>
                                                    </ul>
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>

                            <form action="{{ route('email.index') }}" method="GET">
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

@section('page-specific-style')
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/select2.min.css') }}"/>
<link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
<link href="{{ asset('assets/plugins/table/css/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/table/css/dataTables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('page-specific-script')
<script src="{{ asset('assets/plugins/select2/select2.full.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('assets/plugins/table/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/table.js') }}" type="text/javascript"></script>
<script>
$('[data-init-plugin=select2]').select2();
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