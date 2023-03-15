@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<style>
    .modal-content {
        width: 50%;
        margin: 0 auto;
    }
</style>
<div class="container-fluid">
    <div class="row m-t-30">
        <div class="col-sm-12 col-md-12 col-lg-12">

            <div class="tab-content no-padding m-b-30">
                <div class="tab-pane slide-right active">
                    <div class="card m-b-0">
                        <div class="card-header">
                            <div class="card-title full-width">
                                <h5 class="no-margin">Categories
                                   <button type="button" class="btn btn-info pull-right m-r-5" data-toggle="modal" data-target="#categoryModal">CREATE CATEGORY</button>
                                </h5>
                            </div>
                        </div>
                        @include('backend.categories.includes.form')
                        <div class="card-body">
                            <table class="table table-hover table-responsive-block dataTable with-export custom-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Option</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($data != null)
                                    @php $i = 0; @endphp
                                    @foreach ($data as $row)
                                    @php $i++; @endphp
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ $row->name }}</td>
                                        <td>
                                            @if ($row->is_active == 10)
                                            <strong class="text-success">ACTIVE</strong>
                                            @else
                                            <strong class="text-danger">INACTIVE</strong>
                                            @endif
                                        </td>
                                        <td>{{ $row->created_at }}</td>
                                        <td>
                                            <button type="button" class="btn btn-info m-r-5" data-toggle="modal" data-target="#categoryModal-{{ $row->id }}">EDIT</button>
                                            <button
                                                class="btn {{ $row->is_active == 10 ? 'btn-danger' : 'btn-success' }} m-b-5 btn-change-status"
                                                type="button" data-index="{{ $i }}">
                                                <span>{{ $row->is_active == 10 ? 'DEACTIVATE' : 'ACTIVATE' }}</span>
                                            </button>
                                            <form action="{{ route('categories.change_status', [$row->id]) }}"
                                                method="POST" class="change-status-form-{{ $i }}"
                                                style="display: none;">@csrf @method('PUT')</form>
                                        </td>
                                        @include('backend.categories.includes.edit_form')
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

@section('page-specific-style')
<link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}">
<link href="{{ asset('assets/plugins/table/css/dataTables.bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/table/css/buttons.dataTables.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/plugins/table/css/dataTables.min.css') }}" rel="stylesheet" type="text/css" />
@endsection

@section('page-specific-script')
<script src="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/table/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/table/js/dataTables.buttons.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/table/js/jszip.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/table/js/pdfmake.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/table/js/vfs_fonts.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/table/js/buttons.html5.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/table.js') }}" type="text/javascript"></script>
@endsection