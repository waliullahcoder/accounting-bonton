<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ @$admin_setting->title }}</title>
    <link rel="shortcut icon"
        href="{{ file_exists(@$admin_setting->favicon) ? asset(@$admin_setting->favicon) : asset('backend/images/logo/favicon.png') }}"
        type="image/x-icon">
    @include('layouts.admin.partial.styles')
    @include('layouts.admin.partial.alert')
    <style>
        :root {
            --bs-primary: {{ @$admin_setting->primary_color ?? '#3753e9' }};
            --bs-secondary: {{ @$admin_setting->secondary_color ?? '#415FFF' }};
        }
    </style>
</head>

<body class="bg-light">
    <div class="overflow-hidden site-wrapper @if (Session::has('sidebar-collapse')) session-sidebar @endif">
        @include('layouts.admin.partial.sidebar')

        <div class="content-wrapper">
            @include('layouts.admin.partial.header')
            <div class="content">
                <div class="p-sm-4 p-3">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header pe-2 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="h6 mb-0 text-uppercase py-5px">{{ @$title ?? 'Please Set Title' }}
                                        </h6>
                                        @php
                                            $currentRouteName = \Request::route()->getName();
                                            $create_link = str_replace('index', 'create', $currentRouteName);
                                        @endphp
                                        <div class="d-flex flex-wrap gap-2">
                                            @if (isset($params))
                                                {!! $params !!}
                                            @endif
                                            @if (Auth::user()->hasRole('Software Admin') && !isset($disable_filter) && Auth::user()->can($create_link))
                                                <select name="filter" id="filter"
                                                    class="form-select custom-select flex-shrink-0 select-sm">
                                                    <option value="all">All</option>
                                                    <option value="trash">Trash</option>
                                                </select>
                                            @endif
                                            @if (!isset($inactive_create))
                                                @if (Auth::user()->can($create_link))
                                                    <a href="{{ Route($create_link) }}"
                                                        class="btn btn-primary btn-sm">Add
                                                        New</a>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.admin.partial.footer')
        </div>
    </div>
    <!-- End Site Wrapper -->

    @include('sweetalert::alert')
    @include('layouts.admin.partial.scripts')

    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            $(".date_picker").datepicker({
                format: 'dd-mm-yyyy',
                changeMonth: true,
                changeYear: true,
            }).datepicker('setDate', 'today');

            $(document).on('change', '.change-status', function(e) {
                e.preventDefault();
                let url = $(this).data('url');
                $.ajax({
                    url: url,
                    data: {
                        _method: 'GET',
                        status: 'true',
                    },
                    success: (response) => {
                        if (response.status == 'success') {
                            Swal.fire({
                                width: "22rem",
                                title: "Changed!",
                                text: 'Status Changed Successfully!',
                                icon: "success",
                                showConfirmButton: false,
                                timer: 1500
                            })
                        }
                        if (response.status == 'error') {
                            Swal.fire({
                                width: "22rem",
                                title: "Failed!",
                                text: "You don't have any Authority to do this action",
                                icon: "error",
                                showConfirmButton: false,
                                timer: 1500
                            });
                            $('.dataTable').DataTable().ajax.reload();
                        }
                    }
                });
            });

            $(document).on('click', '.link-delete', function(e) {
                e.preventDefault();
                let url = $(this).data('url');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                            },
                            success: function(response) {
                                if (response.status == 'success') {
                                    $('#editForm').hide();
                                    $('#addForm').show();
                                    Swal.fire({
                                        width: "22rem",
                                        title: "Deleted!",
                                        text: response.status,
                                        icon: "success",
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    $('.dataTable').DataTable().ajax.reload();
                                }
                                if (response.status == 'error') {
                                    $('#editForm').hide();
                                    $('#addForm').show();
                                    Swal.fire({
                                        width: "22rem",
                                        title: "Failed!",
                                        text: "You don't have any Authority to do this action",
                                        icon: "error",
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                }
                            }
                        });
                    } else(
                        result.dismiss === Swal.DismissReason.cancel
                    )
                });
            });

            $("#selectAll").on("click", function(e) {
                if ($(this).is(":checked")) {
                    $(".multi_checkbox").prop("checked", true);
                } else {
                    $(".multi_checkbox").prop("checked", false);
                }
            });

            $(document).on('click', '.trash_delete', function(e) {
                e.preventDefault();
                let url = $(this).data('url');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                parmanent: 'true',
                            },
                            success: function(response) {
                                if (response.status == 'success') {
                                    Swal.fire({
                                        width: "22rem",
                                        title: "Deleted!",
                                        text: response.status,
                                        icon: "success",
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    $('.dataTable').DataTable().ajax.reload();
                                }
                                if (response.status == 'error') {
                                    Swal.fire({
                                        width: "22rem",
                                        title: "Failed!",
                                        text: "You don't have any Authority to do this action",
                                        icon: "error",
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                }
                            }
                        });
                    }
                });
            });

            // multi delete row table
            function multiDelCheckbox(url, selectClass, parmanent) {
                Swal.fire({
                    width: "25rem",
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, delete it!",
                }).then((result) => {
                    if (result.isConfirmed) {
                        let id = [];
                        $("." + selectClass + ":checked").each(function() {
                            id.push($(this).val());
                        });
                        if (id.length > 0) {
                            $.ajax({
                                url: url,
                                type: 'POST',
                                data: {
                                    _method: 'DELETE',
                                    id: id,
                                    parmanent: parmanent
                                },
                                success: function(response) {
                                    if (response.status == 'success') {
                                        $('#editForm').hide();
                                        $('#addForm').show();
                                        Swal.fire({
                                            width: "22rem",
                                            title: "Deleted!",
                                            text: response.val,
                                            icon: "success",
                                            showConfirmButton: false,
                                            timer: 1500
                                        });
                                        $('.dataTable').DataTable().ajax.reload();
                                    }
                                    if (response.status == 'error') {
                                        $('#editForm').hide();
                                        $('#addForm').show();
                                        Swal.fire({
                                            width: "22rem",
                                            title: "Failed!",
                                            text: "You don't have any Authority to do this action",
                                            icon: "error",
                                            showConfirmButton: false,
                                            timer: 1500
                                        });
                                    }
                                    $("#selectAll").prop("checked", false);
                                    $("input[type=checkbox]").prop("checked", false);
                                    $('.dataTable').DataTable().ajax.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                width: "22rem",
                                title: "Error!",
                                text: "Please select atleast one checkbox",
                                icon: "error",
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    }
                });
            }

            $("#trash_bulk_delete").on("click", function() {
                let url = $(this).data("url");
                let selectClass = "trash_multi_checkbox";
                multiDelCheckbox(url, selectClass, 'true');
            });

            $("#trash_selectAll").on("click", function(e) {
                if ($(this).is(":checked")) {
                    $(".trash_multi_checkbox").prop("checked", true);
                } else {
                    $(".trash_multi_checkbox").prop("checked", false);
                }
            });

            $("#bulk_delete").on("click", function() {
                let url = $(this).data("url");
                let selectClass = "multi_checkbox";
                multiDelCheckbox(url, selectClass, 'false');
            });

            $(document).on('click', '.link-recovery', function(e) {
                e.preventDefault();
                let url = $(this).data('url');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, cancel!',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _method: 'DELETE',
                                recovery: 'true',
                            },
                            success: (response) => {
                                if (response.status == 'success') {
                                    Swal.fire({
                                        width: "22rem",
                                        title: "Recovered!",
                                        text: 'Recovered Successfully!',
                                        icon: "success",
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    $('.dataTable').DataTable().ajax.reload();
                                }
                                if (response.status == 'error') {
                                    Swal.fire({
                                        width: "22rem",
                                        title: "Failed!",
                                        text: "You don't have any Authority to do this action",
                                        icon: "error",
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    $('.dataTable').DataTable().ajax.reload();
                                }
                            }
                        });
                    } else(
                        result.dismiss === Swal.DismissReason.cancel
                    )
                });
            });

            $(document).on('change', '#filter', function(e) {
                e.preventDefault();
                if ($(this).val() == 'trash') {
                    $('#regular_all_select').hide();
                    $('#trash_all_select').show();
                    $('#bulk_delete').hide();
                    $('#trash_bulk_delete').show();
                } else {
                    $('#regular_all_select').show();
                    $('#trash_all_select').hide();
                    $('#bulk_delete').show();
                    $('#trash_bulk_delete').hide();
                }
                $('.dataTable').DataTable().ajax.reload();
            });

            $(document).on('click', '.link-print', function(e) {
                e.preventDefault();
                let url = $(this).data('url');
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _method: 'GET',
                    }
                });
            });
        });
    </script>
    @stack('js')
</body>

</html>
