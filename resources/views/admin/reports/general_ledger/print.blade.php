@extends('layouts.admin.print_app')
@section('content')
    <table class="table table-bordered table-condensed table-striped align-middle">
        <thead class="text-nowrap">
            <tr>
                <th class="text-right" colspan="6">Previous Balance</th>
                <th class="text-right">{{ number_format($previousBalance, 2, '.', ',') }}</th>
            </tr>
            <tr>
                <th class="text-center" width="40px">SL#</th>
                <th>Date</th>
                <th>Voucher No</th>
                <th>Particular</th>
                <th class="text-right">Debit</th>
                <th class="text-right">Credit</th>
                <th class="text-right">Balance</th>
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
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ date('d-m-Y', strtotime($row->voucher_date)) }}</td>
                    <td>{{ $row->voucher_no }}</td>
                    <td>{{ $row->narration }}</td>
                    <td class="text-right">{{ number_format($row->debit_amount, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($row->credit_amount, 2, '.', ',') }}</td>
                    <td class="text-right">
                        {{ $balance > 0 ? number_format($balance, 2, '.', ',') : '(' . number_format(abs($balance), 2, '.', ',') . ')' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-white text-right">Total Summary</th>
                <th class="text-white text-right">
                    {{ number_format($totalDebit, 2, '.', ',') }}
                </th>
                <th class="text-white text-right">
                    {{ number_format($totalCredit, 2, '.', ',') }}
                </th>
                <th class="text-white text-right">
                    {{ $balance > 0 ? number_format($balance, 2, '.', ',') : '(' . number_format(abs($balance), 2, '.', ',') . ')' }}
                </th>
            </tr>
        </tfoot>
    </table>
    <div style="padding-top: 10px;">Print Date & Time : {{ date('d-m-Y h:i:s A') }}</div>
@endsection
