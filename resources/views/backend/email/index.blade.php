@extends('layouts.app')

@section('title', 'Email Notifications')

@section('content')
<div class="container-fluid">
    <div class="row m-t-30">
        <div class="col-sm-12 col-md-12 col-lg-12">

            @include('backend.includes.nav')

            <div class="tab-content no-padding m-b-30">
                <div class="tab-pane slide-right active">
                    <div class="card m-b-0">
                        <div class="card-header">
                            <div class="card-title full-width">
                                <h5 class="no-margin">Email Notification Logs</h5>
                            </div>
                            <form action="{{ route('payment.detail.index') }}" method="GET">
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
                        </div>
                        <div class="card-body">
                            <table class="table table-hover table-responsive-block dataTable with-export custom-table">
                                <thead>
                                    <tr>
                                        <th width="15">#</th>
                                        <th width="50">Setup Title</th>
                                        <th width="50">Entry Title</th>
                                        <th width="50">Client</th>
                                        <th width="50">Email</th>
                                        {{-- <th width="50">Payment Date</th> --}}
                                        <th width="50">Sent At</th>
                                        <th width="100">Option</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($data != null)
                                    @php $i = 0; @endphp
                                    @foreach ($data as $row)
                                    @php
                                    $i++;
                                    $listing = json_decode($row->data, true);
                                    @endphp
                                    <tr>
                                        <td>{{ $i }}</td>
                                        @if($row->payment_entry)
                                        <td>{{ $row->payment_entry->setup->title }}</td>
                                        <td>{{ $row->payment_entry->title }}</td>
                                        @elseif($row->payment_detail)
                                        <td>{{ $row->payment_detail->setup->title }}</td>
                                        <td>{{ $row->payment_detail->title }}</td>
                                        @else
                                        <td>N/A</td>
                                        <td>N/A</td>
                                        @endif
                                        <td>{{ $row->client->name }}</td>
                                        <td>{{ $row->client->email }}</td>
                                        <td>{{ $row->created_at }}</td>
                                        <td class="list-item">
                                            <button class="btn btn-primary m-b-5 btn-view-more"
                                                type="button">VIEW</button>

                                            {{-- MODEL ITEM START --}}
                                            @if ($row->payment_entry)
                                            <input type="hidden" data-title="setup_title"
                                                value="{{ $row->payment_entry->setup->title }}" class="payment-item">
                                            <input type="hidden" data-title="entry_title"
                                                value="{{ $row->payment_entry->title }}" class="payment-item">
                                            @endif

                                            @if ($row->payment_detail)
                                            <input type="hidden" data-title="setup_title"
                                                value="{{ $row->payment_detail->setup->title }}" class="payment-item">
                                            <input type="hidden" data-title="entry_title"
                                                value="{{ $row->payment_detail->title }}" class="payment-item">
                                            @endif
                                            <input type="hidden" data-title="client" value="{{ $row->client->name }}"
                                                class="payment-item">
                                            <input type="hidden" data-title="email" value="{{ $row->client->email }}"
                                                class="payment-item">

                                            @foreach ($listing as $key => $item)
                                            @if($key == 'link')
                                            @php
                                            $link = "<span data-copy=". $item ." data-title='Link'
                                                class='btn-copy-to-clipboard text-danger cursor-pointer'>Click here to copy the link</span>";

                                            @endphp
                                            <input type="hidden" data-title="{{ $key }}" value="{!! $link !!}"
                                                class="payment-item">
                                            @else
                                            <input type="hidden" data-title="{{ $key }}" value="{{ $item }}"
                                                class="payment-item">
                                            @endif
                                            @endforeach
                                            <input type="hidden" data-title="sent_at" value="{{ $row->created_at }}"
                                                class="payment-item">
                                            @php
                                                if($row->payment_entry){
                                                    $content = $row->payment_entry->contents;
                                                }elseif($row->payment_detail){
                                                    $content = $row->payment_detail->contents;
                                                }else{
                                                    $content = '{}';
                                                }
                                            @endphp
                                            <input type="hidden" value="{{ $content }}" class="contents">
                                            {{-- MODEL ITEM END --}}
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

{{-- MODAL START --}}
<div class="modal fade slide-right show" id="show-details-modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm" style="max-width: 800px; margin: 0 auto;">
        <div class="modal-content-wrapper">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Email Detail:</h5>
                            <pre class="m-t-0" id="payment-detail"></pre>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Email Payment Items:</h5>
                            <pre class="m-t-0" id="payment-contents"></pre>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <button type="button" class="btn btn-lg btn-danger m-t-5 pull-right"
                                data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- MODAL END --}}

@endsection
{{-- @include('backend.payment.setup.asset_index') --}}

@section('page-specific-style')
<link rel="stylesheet" href="{{ asset('assets/plugins/select2/select2.min.css') }}" />
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
$(document).on('click', '.btn-view-more', function (e) {
    $('#payment-transaction').parents('.row').hide();
    $list_item = $(this).parents('.list-item');
    
    var detail = {};
    $list_item.find(".payment-item").each( function() {
    detail[$(this).data('title')] = $(this).val();
    });
    var contents = JSON.parse($list_item.find('.contents').val());
    document.querySelector('#payment-contents').innerHTML = JSON.stringify(contents, null, 3);
    
    document.querySelector('#payment-detail').innerHTML = JSON.stringify(detail, null, 3);

    $('#show-details-modal').modal('show');
});
</script>
@endsection