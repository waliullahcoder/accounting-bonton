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
                    @if (Auth::user()->hasRole('Software Admin'))
                        <th>Company</th>
                    @endif
                    <th>Date</th>
                    <th>Voucher No</th>
                    <th>Debit Head</th>
                    <th>Credit Head</th>
                    <th>Amount</th>
                    <th>Account Status</th>
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
                    type: "GET",
                    data: function(data) {
                        data.type = $('#filter').val();
                    }
                },
                columns: [{
                        data: "DT_RowIndex",
                        name: "DT_RowIndex",
                        className: "text-center",
                        orderable: false,
                        searchable: false,
                        width: '40'
                    },
                    @if (Auth::user()->hasRole('Software Admin'))
                        {
                            data: 'company.name',
                            name: 'company.name',
                        },
                    @endif {
                        data: 'voucher_date',
                        name: 'voucher_date',
                        orderable: false,
                        searchable: false,
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
                        data: 'debit_amount',
                        name: 'debit_amount'
                    },
                    {
                        data: 'approve',
                        name: 'approve',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: "text-end",
                    },
                ],
                "fnDrawCallback": function(oSettings) {
                    const tooltips = document.querySelectorAll('.tt');
                    tooltips.forEach(t => {
                        new bootstrap.Tooltip(t);
                    });
                }
            });
        });
    </script>
@endpush
