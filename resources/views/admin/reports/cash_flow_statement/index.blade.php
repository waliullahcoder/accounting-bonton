@extends('layouts.admin.report_app')

@section('form')
    <input type="hidden" name="print" value="">
    <input type="hidden" name="filter" value="1">
    <div class="row g-3">
        <div class="col-6">
            <label for="project_id" class="form-label"><b>Project Name</b></label>
            <select name="project_id" id="project_id" class="form-select select" data-placeholder="select Project Name">
                <option value=""></option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6">
            <label for="date_range" class="form-label"><b>Date</b></label>
            <input type="text" class="form-control date-range" name="date_range" id="date_range"
                placeholder="Select Date Range" data-time-picker="true" data-format="DD-MM-Y" data-separator=" to "
                autocomplete="off" required
                value="{{ date('d-m-Y', strtotime($start_date)) . ' to ' . date('d-m-Y', strtotime($end_date)) }}">
        </div>
    </div>
@endsection

@section('content')
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th class="text-center px-3" width="40px">SL#</th>
                    <th class="px-3">Date</th>
                    <th class="text-end px-3">Cash Opening</th>
                    <th class="text-end px-3">Cash Receive</th>
                    <th class="text-end px-3">Cash Payment</th>
                    <th class="text-end px-3">Cash Balance</th>
                    <th class="text-end px-3">Bank Opening</th>
                    <th class="text-end px-3">Bank Receive</th>
                    <th class="text-end px-3">Bank Payment</th>
                    <th class="text-end px-3">Bank Balance</th>
                    <th class="text-end px-3">Total Balance</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_cash_receive = 0;
                    $total_cash_payment = 0;
                    $total_bank_receive = 0;
                    $total_bank_payment = 0;
                @endphp
                @foreach ($data as $row)
                    @php
                        $lastDate = Date('Y-m-d', strtotime('-1 day', strtotime($row->voucher_date)));
                        // Cash
                        $openRecCashAmount = \App\Models\AccountTransaction::when(request('project_id'), function (
                            $query,
                        ) {
                            $query->where('project_id', request('project_id'));
                        })
                            ->where('voucher_date', '<=', $lastDate)
                            ->whereIn('voucher_type', ['CV', 'JV'])
                            ->where('coa_head_code', 'like', $generalLedgerHeadCash . '%')
                            ->sum('debit_amount');
                        $openPayCashAmount = \App\Models\AccountTransaction::when(request('project_id'), function (
                            $query,
                        ) {
                            $query->where('project_id', request('project_id'));
                        })
                            ->where('voucher_date', '<=', $lastDate)
                            ->whereIn('voucher_type', ['DV', 'JV'])
                            ->where('coa_head_code', 'like', $generalLedgerHeadCash . '%')
                            ->sum('credit_amount');
                        $recCashAmount = \App\Models\AccountTransaction::when(request('project_id'), function ($query) {
                            $query->where('project_id', request('project_id'));
                        })
                            ->where('voucher_date', $row->voucher_date)
                            ->whereIn('voucher_type', ['CV', 'JV'])
                            ->where('coa_head_code', 'like', $generalLedgerHeadCash . '%')
                            ->sum('debit_amount');
                        $payCashAmount = \App\Models\AccountTransaction::when(request('project_id'), function ($query) {
                            $query->where('project_id', request('project_id'));
                        })
                            ->where('voucher_date', $row->voucher_date)
                            ->whereIn('voucher_type', ['DV', 'JV'])
                            ->where('coa_head_code', 'like', $generalLedgerHeadCash . '%')
                            ->sum('credit_amount');
                        // Bank
                        $openRecBankAmount = \App\Models\AccountTransaction::when(request('project_id'), function (
                            $query,
                        ) {
                            $query->where('project_id', request('project_id'));
                        })
                            ->where('voucher_date', '<=', $lastDate)
                            ->whereIn('voucher_type', ['CV', 'JV'])
                            ->where('coa_head_code', 'like', $generalLedgerHeadBank . '%')
                            ->sum('debit_amount');
                        $openPayBankAmount = \App\Models\AccountTransaction::when(request('project_id'), function (
                            $query,
                        ) {
                            $query->where('project_id', request('project_id'));
                        })
                            ->where('voucher_date', '<=', $lastDate)
                            ->whereIn('voucher_type', ['DV', 'JV'])
                            ->where('coa_head_code', 'like', $generalLedgerHeadBank . '%')
                            ->sum('credit_amount');
                        $recBankAmount = \App\Models\AccountTransaction::when(request('project_id'), function ($query) {
                            $query->where('project_id', request('project_id'));
                        })
                            ->where('voucher_date', $row->voucher_date)
                            ->whereIn('voucher_type', ['CV', 'JV'])
                            ->where('coa_head_code', 'like', $generalLedgerHeadBank . '%')
                            ->sum('debit_amount');
                        $payBankAmount = \App\Models\AccountTransaction::when(request('project_id'), function ($query) {
                            $query->where('project_id', request('project_id'));
                        })
                            ->where('voucher_date', $row->voucher_date)
                            ->whereIn('voucher_type', ['DV', 'JV'])
                            ->where('coa_head_code', 'like', $generalLedgerHeadBank . '%')
                            ->sum('credit_amount');

                        $total_cash_receive += $recCashAmount;
                        $total_cash_payment += $payCashAmount;
                        $total_bank_receive += $recBankAmount;
                        $total_bank_payment += $payBankAmount;
                    @endphp
                    <tr>
                        <td class="px-3 text-center">{{ $loop->iteration }}</td>
                        <td class="px-3">{{ date('d-m-Y', strtotime($row->voucher_date)) }}</td>
                        <td class="px-3 text-end">{{ number_format($openRecCashAmount - $openPayCashAmount, 2, '.', '') }}
                        </td>
                        <td class="px-3 text-end">{{ number_format($recCashAmount, 2, '.', '') }}</td>
                        <td class="px-3 text-end">{{ number_format($payCashAmount, 2, '.', '') }}</td>
                        <td class="px-3 text-end">
                            {{ number_format($openRecCashAmount - $openPayCashAmount + $recCashAmount - $payCashAmount, 2, '.', '') }}
                        </td>
                        <td class="px-3 text-end">{{ number_format($openRecBankAmount - $openPayBankAmount, 2, '.', '') }}
                        </td>
                        <td class="px-3 text-end">{{ number_format($recBankAmount, 2, '.', '') }}</td>
                        <td class="px-3 text-end">{{ number_format($payBankAmount, 2, '.', '') }}</td>
                        <td class="px-3 text-end">
                            {{ number_format($openRecBankAmount - $openPayBankAmount + $recBankAmount - $payBankAmount, 2, '.', '') }}
                        </td>
                        <td class="px-3 text-end">
                            {{ number_format($openRecCashAmount - $openPayCashAmount + $recCashAmount - $payCashAmount + ($openRecBankAmount - $openPayBankAmount + $recBankAmount - $payBankAmount), 2, '.', '') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <th class="text-end" colspan="2">Total Summary</th>
                <th class="text-end"></th>
                <th class="text-end">{{ number_format($total_cash_receive, 2, '.', '') }}</th>
                <th class="text-end">{{ number_format($total_cash_payment, 2, '.', '') }}</th>
                <th class="text-end"></th>
                <th class="text-end"></th>
                <th class="text-end">{{ number_format($total_bank_receive, 2, '.', '') }}</th>
                <th class="text-end">{{ number_format($total_bank_payment, 2, '.', '') }}</th>
                <th class="text-end"></th>
                <th class="text-end"></th>
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
                e.preventDefault();
                $('input[name="print"]').val('true');
                $('.filter_form')[0].setAttribute("target", "_blank");
                $('.filter_form').submit();
            });

            $(document).on('click', '#filter_btn', function(e) {
                $('input[name="print"]').val('');
                $('.filter_form')[0].setAttribute("target", "_self");
            });
        });
    </script>
@endpush
