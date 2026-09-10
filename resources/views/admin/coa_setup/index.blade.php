@extends('layouts.admin.index_app')
@push('css')
    <link href="{{ asset('backend/css/TreeMenu.css') }}" rel="stylesheet">
@endpush

@section('content')
    @php
        $currentRouteName = \Request::route()->getName();
        $store_link = route(str_replace('index', 'store', $currentRouteName));
        $create_link = route(str_replace('index', 'create', $currentRouteName));
    @endphp
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="tree">
                    <ul id="makeTree">
                        {!! $html !!}
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <form action="{{ $store_link }}" method="POST" id="coa_form">
                    @csrf
                    <div class="row g-2">
                        <div class="col-12">
                            <label for="head_code" class="form-label mb-1"><b>Head Code <span
                                        class="text-danger">*</span></b></label>
                            <input type="number" class="form-control" id="head_code" name="head_code" readonly
                                required>
                        </div>
                        <div class="col-12">
                            <label for="parent_head" class="form-label mb-1"><b>Parent Head</b></label>
                            <input type="text" class="form-control" id="parent_head" name="parent_head"
                                readonly>
                            <input type="hidden" name="parent_id" id="parent_id">
                        </div>
                        <div class="col-12">
                            <label for="head_name" class="form-label mb-1"><b>Head Name <span
                                        class="text-danger">*</span></b></label>
                            <input type="text" class="form-control" id="head_name" name="head_name" required
                                placeholder="Head Name">
                        </div>
                        <div class="col-12 d-none">
                            <label for="head_type" class="form-label mb-1"><b>Head Type <span
                                        class="text-danger">*</span></b></label>
                            <input type="text" class="form-control" id="head_type" name="head_type" readonly
                                required>
                        </div>
                        <div class="col-12">
                            <div class="py-2">
                                <div class="d-inline-block me-3 custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="status" name="status"
                                        checked>
                                    <label class="custom-control-label" for="status">
                                        <b class="ms-2">Active</b>
                                    </label>
                                </div>
                                <div class="me-3 custom-control custom-checkbox" id="transaction_area"
                                    style="display: inline-block;">
                                    <input type="checkbox" class="custom-control-input" id="transaction" name="transaction">
                                    <label class="custom-control-label" for="transaction">
                                        <b class="ms-2">Transaction Ledger</b>
                                    </label>
                                </div>
                                <div class="me-3 custom-control custom-checkbox" id="general_area"
                                    style="display: inline-block;">
                                    <input type="checkbox" class="custom-control-input" id="general" name="general">
                                    <label class="custom-control-label" for="general">
                                        <b class="ms-2">General Ledger</b>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-outline-info me-1 btn-sm" id="btnNew" name="btnNew"
                                disabled onclick="btnNewClick()"><i class="fas fa-plus"></i>
                                New</button>
                            <button type="submit" class="btn btn-outline-info me-1 btn-sm" id="btnAction"
                                name="btnAction"><i class="fas fa-plus"></i> Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('backend/js/TreeMenu.js') }}"></script>
    <script type="text/javascript">
        function loadData(id) {
            $.ajax({
                url: "{{ $create_link }}",
                type: 'POST',
                data: {
                    _method: 'GET',
                    id: id
                },
                success: function(response) {
                    if (response.status == 'success') {
                        $("#head_name").focus();
                        $('#head_code').val(response.data.head_code);
                        $('#head_name').val(response.data.head_name);
                        $('#head_type').val(response.data.head_type);
                        $('#parent_head').val(response.parent_head_name);
                        $('#btnAction').html('<i class="far fa-edit"></i> Update');
                        $('#coa_form').attr('action', response.form_link);
                        if ($('#method').length == 0) {
                            $('#coa_form').prepend(
                                '<input name="_method" id="method" type="hidden" value="PUT">');
                        }
                        if (response.data.status == 1) {
                            $("#status").prop("checked", true);
                        } else {
                            $("#status").prop("checked", false);
                        }
                        if (response.data.transaction == 1) {
                            $("#btnNew").attr("disabled", true);
                            $("#transaction").prop("checked", true);
                        } else {
                            $("#btnNew").attr("disabled", false);
                            $("#transaction").prop("checked", false);
                        }
                        $('#general_area').show();
                        if (response.children > 0) {
                            $("#transaction").prop("checked", false);
                            $("#transaction_area").hide();
                        } else {
                            $("#transaction_area").show();
                        }
                        if (response.data.general == 1) {
                            $("#general").prop("checked", true);
                        } else {
                            $("#general").prop("checked", false);
                        }
                        if (response.parent_head_general == 1 || response.hasGL == 1) {
                            $("#general").prop("checked", false);
                            $('#general_area').hide();
                        }
                        $('#parent_id').val(response.data.id);
                    }
                }
            });
        }

        function btnNewClick() {
            if ($("#transaction").prop('checked') != true) {
                var parent_id = $('#parent_id').val();
                $.ajax({
                    url: "{{ $create_link }}",
                    type: 'POST',
                    data: {
                        _method: 'GET',
                        parent_id: parent_id
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#parent_head').val(response.parent_head);
                            $('#head_code').val(response.head_code);
                            $('#head_name').val('');
                            $("#head_name").focus();
                            $('#coa_form').attr('action', '{{ $store_link }}');
                            $('#btnAction').html('<i class="fas fa-plus"></i> Save');
                            $('#method').remove();
                            $("#transaction_area").show();
                            if ($("#general").is(':checked')) {
                                $("#general").prop('checked', false);
                                $("#transaction").prop('checked', true);
                                $('#general_area').hide();
                            } else {
                                $('#general_area').show();
                            }
                        }
                    }
                });
            } else {
                Swal.fire({
                    width: "22rem",
                    position: 'top-right',
                    text: "Can't Make New Head!",
                    icon: "error",
                    toast: true,
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        }

        $(document).ready(function() {
            make_tree_menu('makeTree');
        });
    </script>
@endpush
