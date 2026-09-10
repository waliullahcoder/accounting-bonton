@extends('layouts.admin.index_app')

@section('content')
    @php
        $currentRouteName = \Request::route()->getName();
        $link = Route($currentRouteName);
    @endphp
    <div class="card-body">
        <table class="dataTable table align-middle" style="width:100%">
            <thead>
                <tr class="text-nowrap">
                    <th width="40">SL#</th>
                    <th>Project</th>
                    <th>Date</th>
                    <th>Voucher Type</th>
                    <th>Voucher No</th>
                    <th>Debit Head</th>
                    <th>Credit Head</th>
                    <th>Amount</th>
                    <th width="110" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('.dataTable').dataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: {
                    url: "{{ $link }}",
                    type: "GET"
                },
                columns: [{
                        data: "DT_RowIndex",
                        name: "DT_RowIndex",
                        className: "text-center",
                        orderable: false,
                        searchable: false,
                        width: '40'
                    },
                    {
                        data: 'project.name',
                        name: 'project.name',
                        defaultContent: 'N/A'
                    },
                    {
                        data: 'voucher_date',
                        name: 'voucher_date',
                        orderable: false,
                        searchable: false,
                        className: "text-nowrap",
                    },
                    {
                        data: 'voucher_type',
                        name: 'voucher_type',
                    },
                    {
                        data: 'voucher_no',
                        name: 'voucher_no',
                    },
                    {
                        data: 'debit_head',
                        name: 'debit_head',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'credit_head',
                        name: 'credit_head',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'amount',
                        name: 'amount',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: "text-center",
                    },
                ],
                "fnDrawCallback": function(oSettings) {
                    const tooltips = document.querySelectorAll('.tt');
                    tooltips.forEach(t => {
                        new bootstrap.Tooltip(t);
                    });
                }
            });

            $(document).on('click', '.approve-btn', function(e) {
                e.preventDefault();
                Swal.fire({
                    width: "25rem",
                    title: "Are you sure?",
                    text: "You won't be able to revert this!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, refuse it!",
                }).then((result) => {
                    if (result.value) {
                        let url = $(this).data('url');
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: {
                                _method: 'GET'
                            },
                            success: function(response) {
                                if (response.status == 'success') {
                                    Swal.fire({
                                        width: "22rem",
                                        title: "Refused!",
                                        text: 'Refused Successfully',
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
            })
        });
    </script>
@endpush
