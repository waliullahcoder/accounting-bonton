@extends('layouts.admin.print_app')
@push('css')
    <style>
        @media screen,
        print {
            tbody tr td {
                font-size: 12px;
            }
        }
    </style>
@endpush
@section('content')
    <table class="table table-bordered table-condensed table-striped align-middle">
        <thead class="text-nowrap">
            <tr>
                <th class="text-center" width="40px">SL#</th>
                <th>Date</th>
                <th class="text-right">Cash Opening</th>
                <th class="text-right">Cash Receive</th>
                <th class="text-right">Cash Payment</th>
                <th class="text-right">Cash Balance</th>
                <th class="text-right">Bank Opening</th>
                <th class="text-right">Bank Receive</th>
                <th class="text-right">Bank Payment</th>
                <th class="text-right">Bank Balance</th>
                <th class="text-right">Total Balance</th>
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
                    $openRecCashAmount = \App\Models\AccountTransaction::when(request('project_id'), function ($query) {
                        $query->where('project_id', request('project_id'));
                    })
                        ->where('voucher_date', '<=', $lastDate)
                        ->whereIn('voucher_type', ['CV', 'JV'])
                        ->where('coa_head_code', 'like', $generalLedgerHeadCash . '%')
                        ->sum('debit_amount');
                    $openPayCashAmount = \App\Models\AccountTransaction::when(request('project_id'), function ($query) {
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
                    $openRecBankAmount = \App\Models\AccountTransaction::when(request('project_id'), function ($query) {
                        $query->where('project_id', request('project_id'));
                    })
                        ->where('voucher_date', '<=', $lastDate)
                        ->whereIn('voucher_type', ['CV', 'JV'])
                        ->where('coa_head_code', 'like', $generalLedgerHeadBank . '%')
                        ->sum('debit_amount');
                    $openPayBankAmount = \App\Models\AccountTransaction::when(request('project_id'), function ($query) {
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
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td class="text-nowrap">{{ date('d-m-Y', strtotime($row->voucher_date)) }}</td>
                    <td class="text-right">{{ number_format($openRecCashAmount - $openPayCashAmount, 2, '.', '') }}
                    </td>
                    <td class="text-right">{{ number_format($recCashAmount, 2, '.', '') }}</td>
                    <td class="text-right">{{ number_format($payCashAmount, 2, '.', '') }}</td>
                    <td class="text-right">
                        {{ number_format($openRecCashAmount - $openPayCashAmount + $recCashAmount - $payCashAmount, 2, '.', '') }}
                    </td>
                    <td class="text-right">{{ number_format($openRecBankAmount - $openPayBankAmount, 2, '.', '') }}
                    </td>
                    <td class="text-right">{{ number_format($recBankAmount, 2, '.', '') }}</td>
                    <td class="text-right">{{ number_format($payBankAmount, 2, '.', '') }}</td>
                    <td class="text-right">
                        {{ number_format($openRecBankAmount - $openPayBankAmount + $recBankAmount - $payBankAmount, 2, '.', '') }}
                    </td>
                    <td class="text-right">
                        {{ number_format($openRecCashAmount - $openPayCashAmount + $recCashAmount - $payCashAmount + ($openRecBankAmount - $openPayBankAmount + $recBankAmount - $payBankAmount), 2, '.', '') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <th class="text-right" colspan="2">Total Summary</th>
            <th class="text-right"></th>
            <th class="text-right">{{ number_format($total_cash_receive, 2, '.', '') }}</th>
            <th class="text-right">{{ number_format($total_cash_payment, 2, '.', '') }}</th>
            <th class="text-right"></th>
            <th class="text-right"></th>
            <th class="text-right">{{ number_format($total_bank_receive, 2, '.', '') }}</th>
            <th class="text-right">{{ number_format($total_bank_payment, 2, '.', '') }}</th>
            <th class="text-right"></th>
            <th class="text-right"></th>
        </tfoot>
    </table>
    <div style="padding-top: 10px;">Print Date & Time : {{ date('d-m-Y h:i:s A') }}</div>
@endsection
