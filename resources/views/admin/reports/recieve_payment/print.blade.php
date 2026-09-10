@extends('layouts.admin.print_app')
@section('content')
    <div class="mb-3">
        <table class="table table-bordered table-sm mb-0">
            <thead>
                <tr>
                    <th colspan="4" class="text-center">Receives</th>
                </tr>
                <tr>
                    <th class="text-center" width="60">SL#</th>
                    <th>Head Code</th>
                    <th>Head Name</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <th class="text-center">{{ $loop->iteration }}</th>
                        <td>{{ $row->coa->head_code }}</td>
                        <td>{{ $row->coa->head_name }}</td>
                        <td class="text-right">{{ number_format($row->debit_amount) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right">Total</th>
                    <th class="text-right">{{ number_format($data->sum('debit_amount')) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mb-3">
        <table class="table table-bordered table-sm mb-0">
            <thead>
                <tr>
                    <th colspan="4" class="text-center">Payments</th>
                </tr>
                <tr>
                    <th class="text-center" width="60">SL#</th>
                    <th>Head Code</th>
                    <th>Head Name</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <th class="text-center">{{ $loop->iteration }}</th>
                        <td>{{ $row->coa->head_code }}</td>
                        <td>{{ $row->coa->head_name }}</td>
                        <td class="text-right">{{ number_format($row->credit_amount) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right">Total</th>
                    <th class="text-right">{{ number_format($data->sum('credit_amount')) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mb-3">
        @if ($data->sum('debit_amount') > $data->sum('credit_amount'))
            <h5 class="text-center mb-0" style="background-color: #00c292; color: #fff; padding: 0.5rem 0;">Net Balance:
                {{ number_format($data->sum('debit_amount') - $data->sum('credit_amount')) }}
            </h5>
        @elseif($data->sum('debit_amount') < $data->sum('credit_amount'))
            <h5 class="text-center mb-0" style="background-color: #DC3545; color: #fff; padding: 0.5rem 0;">Net Balance:
                {{ number_format($data->sum('credit_amount') - $data->sum('debit_amount')) }}
            </h5>
        @endif
    </div>
    <div style="padding-top: 10px;">Print Date & Time : {{ date('d-m-Y h:i:s A') }}</div>
@endsection
