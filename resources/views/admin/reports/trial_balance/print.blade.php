@extends('layouts.admin.print_app')
@section('content')
    <table class="table table-bordered table-condensed table-striped align-middle">
        <thead>
            <tr>
                <th class="text-center" width="20px">Sl#</th>
                <th>General Ledger Head</th>
                <th class="text-right">Debit Amount</th>
                <th class="text-right">Credit Amount</th>
            </tr>
        </thead>
        @php
            $sl = 0;
        @endphp
        <tbody>
            @foreach ($coaLists as $row)
                <tr>
                    <td class="text-center">{{ $sl++ }}</td>
                    <td>{{ $row->coa_setup->head_name }} - {{ $row->coa_setup->head_code }}</td>
                    <td class="text-right">{{ number_format($row->debit_amount, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($row->credit_amount, 2, '.', ',') }}</td>
                </tr>
            @endforeach
            @foreach ($coaLists1 as $row)
                <tr>
                    <td class="text-center">{{ $sl++ }}</td>
                    <td>{{ $row->parent_head->head_name }} - {{ $row->parent_head->head_code }}</td>
                    <td class="text-right">{{ number_format($row->debit_amount, 2, '.', ',') }}</td>
                    <td class="text-right">{{ number_format($row->credit_amount, 2, '.', ',') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th class="text-right" colspan="2">Total Summary</th>
                <th class="text-right">
                    {{ number_format($coaLists->sum('debit_amount') + $coaLists1->sum('debit_amount'), 2, '.', ',') }}
                </th>
                <th class="text-right">
                    {{ number_format($coaLists->sum('credit_amount') + $coaLists1->sum('credit_amount'), 2, '.', ',') }}
                </th>
            </tr>
        </tfoot>
    </table>
    <div style="padding-top: 10px;">Print Date & Time : {{ date('d-m-Y h:i:s A') }}</div>
@endsection
