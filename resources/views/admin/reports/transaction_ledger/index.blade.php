@extends('layouts.admin.report_app')

@section('form')
    <input type="hidden" name="print" value="">
    <input type="hidden" name="filter" value="1">
    <div class="row g-3">
        <div class="col-sm-4">
            <label for="project_id" class="form-label"><b>Project Name</b></label>
            <select name="project_id" id="project_id" class="form-select select" data-placeholder="select Project Name">
                <option value=""></option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-4">
            <label for="coa_setup_id" class="form-label"><b>Voucher Account <span class="text-danger">*</span></b></label>
            <select name="coa_setup_id" id="coa_setup_id" class="form-select select" data-placeholder="Select Account"
                required>
                <option value=""></option>
                @foreach ($coas as $item)
                    <option value="{{ $item->id }}" {{ $item->id == request('coa_setup_id') ? 'selected' : '' }}>
                        {{ $item->head_name }} - {{ $item->head_code }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-4">
            <label for="date_range" class="form-label"><b>Date <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control date-range" name="date_range" id="date_range"
                placeholder="Select Date Range" data-time-picker="true" data-format="DD-MM-Y" data-separator=" to "
                autocomplete="off"
                value="{{ date('d-m-Y', strtotime($start_date)) . ' to ' . date('d-m-Y', strtotime($end_date)) }}" required>
        </div>
    </div>
@endsection

@section('content')
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th class="px-3 text-end" colspan="6">Previous Balance</th>
                    <th class="px-3 text-end">{{ number_format($previousBalance, 2, '.', ',') }}</th>
                </tr>
                <tr>
                    <th class="text-center px-3" width="40px">SL#</th>
                    <th class="px-3">Date</th>
                    <th class="px-3">Voucher No</th>
                    <th class="px-3">Particular</th>
                    <th class="px-3 text-end">Debit</th>
                    <th class="px-3 text-end">Credit</th>
                    <th class="px-3 text-end">Balance</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $balance = $previousBalance;
                    $totalDebit = 0;
                    $totalCredit = 0;
                @endphp
                @foreach ($data as $row)
                    @php
                        $balance += $row->debit_amount - $row->credit_amount;
                        $totalDebit += $row->debit_amount;
                        $totalCredit += $row->credit_amount;
                    @endphp
                    <tr>
                        <td class="px-3 text-center">{{ $loop->iteration }}</td>
                        <td class="px-3">{{ date('d-m-Y', strtotime($row->voucher_date)) }}</td>
                        <td class="px-3">{{ $row->voucher_no }}</td>
                        <td class="px-3">{{ $row->narration }}</td>
                        <td class="px-3 text-end">{{ number_format($row->debit_amount, 2, '.', ',') }}</td>
                        <td class="px-3 text-end">{{ number_format($row->credit_amount, 2, '.', ',') }}</td>
                        <td class="px-3 text-end">
                            {{ $balance > 0 ? number_format($balance, 2, '.', ',') : '(' . number_format(abs($balance), 2, '.', ',') . ')' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-primary">
                    <th colspan="4" class="text-white text-end px-3">Total Summary</th>
                    <th class="text-white text-end px-3">
                        {{ number_format($totalDebit, 2, '.', ',') }}
                    </th>
                    <th class="text-white text-end px-3">
                        {{ number_format($totalCredit, 2, '.', ',') }}
                    </th>
                    <th class="text-white text-end px-3">
                        {{ $balance > 0 ? number_format($balance, 2, '.', ',') : '(' . number_format(abs($balance), 2, '.', ',') . ')' }}
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "order": false,
                dom: 'Bfrtip',
                buttons: [
                    'excelHtml5',
                    {
                        'text': '<i class="fal fa-file-pdf"></i> Print',
                        'className': 'getPdf',
                    },
                ]
            });

            $(document).on('click', '.getPdf', function(e) {
                $('input[name="print"]').val('true');
                $('.filter_form')[0].setAttribute("target", "_blank");
                $('.filter_form')[0].submit();
            });

            $(document).on('click', '#filter_btn', function(e) {
                $('input[name="print"]').val('');
                $('.filter_form')[0].setAttribute("target", "_self");
            });
        });
    </script>
@endpush
