@extends('layouts.admin.print_app')
@section('content')
    <table class="table table-bordered table-condensed table-striped align-middle">
        <thead class="text-nowrap">
            <tr>
                <th class="text-center" width="30">SL#</th>
                <th>Head Name</th>
                <th>Head Code</th>
                <th class="text-right">Debit Amount</th>
                <th class="text-right">Credit Amount</th>
                <th class="text-right">Balance</th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_balance = 0;
            @endphp
            @foreach ($data as $row)
                <tr>
                    <td class="text-center" width="30">{{ $loop->iteration }}</td>
                    <td>{{ @$row->coa->head_name }}</td>
                    <td>{{ @$row->coa->head_code }}</td>
                    <td class="text-right">{{ $row->debit_amount }}</td>
                    <td class="text-right">{{ $row->credit_amount }}</td>
                    <td class="text-right">
                        @php
                            if (@$row->coa->head_type == 'I' || @$row->coa->head_type == 'L') {
                                $balance = $row->credit_amount - $row->debit_amount;
                            } else {
                                $balance = $row->debit_amount - $row->credit_amount;
                            }
                            $total_balance += $balance;
                        @endphp
                        @if ($balance < 0)
                            ({{ number_format(abs($balance), 2, '.', ',') }})
                        @else
                            {{ number_format($balance, 2, '.', ',') }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total Summary</th>
                <th class="text-right">{{ number_format($data->sum('debit_amount'), 2, '.', ',') }}</th>
                <th class="text-right">{{ number_format($data->sum('credit_amount'), 2, '.', ',') }}</th>
                <th class="text-right">
                    @if ($total_balance < 0)
                        ({{ number_format(abs($total_balance), 2, '.', ',') }})
                    @else
                        {{ number_format($total_balance, 2, '.', ',') }}
                    @endif
                </th>
            </tr>
        </tfoot>
    </table>
    <div style="padding-top: 10px;">Print Date & Time : {{ date('d-m-Y h:i:s A') }}</div>
@endsection
