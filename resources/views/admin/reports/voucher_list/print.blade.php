@extends('layouts.admin.print_app')
@section('content')
    <table class="table table-bordered table-condensed table-striped align-middle">
        <thead class="text-nowrap">
            <tr>
                <th class="text-center" width="30px">SL#</th>
                <th>Date</th>
                <th>Project Name</th>
                <th>Voucher Type</th>
                <th>Voucher No</th>
                <th>Debit Head</th>
                <th>Credit Head</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    <td class="text-center" width="30px">{{ $loop->iteration }}</td>
                    <td>{{ date('d-m-Y', strtotime($row->voucher_date)) }}</td>
                    <td>{{ $row->project->name ?? 'N/A' }}</td>
                    <td>{{ $row->voucher_type }}</td>
                    <td>{{ $row->voucher_no }}</td>
                    @php
                        $debit_heads = App\Models\AccountTransaction::with('coa')
                            ->where('voucher_no', $row->voucher_no)
                            ->where('voucher_type', $row->voucher_type)
                            ->where('debit_amount', '>', 0)
                            ->get('coa_setup_id')
                            ->pluck('coa.head_name')
                            ->toArray();
                        $credit_heads = App\Models\AccountTransaction::with('coa')
                            ->where('voucher_no', $row->voucher_no)
                            ->where('voucher_type', $row->voucher_type)
                            ->where('credit_amount', '>', 0)
                            ->get('coa_setup_id')
                            ->pluck('coa.head_name')
                            ->toArray();
                    @endphp
                    <td>
                        @foreach ($debit_heads as $item)
                            {{ $loop->iteration > 1 ? ', ' : '' }} {{ $item }}
                        @endforeach
                    </td>
                    <td>
                        @foreach ($credit_heads as $item)
                            {{ $loop->iteration > 1 ? ', ' : '' }} {{ $item }}
                        @endforeach
                    </td>
                    <td class="text-right">{{ $row->amount }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th class="text-right" colspan="6">Total Summary</th>
                <th class="text-right">{{ $data->sum('amount') }}</th>
            </tr>
        </tfoot>
    </table>
    <div style="padding-top: 10px;">Print Date & Time : {{ date('d-m-Y h:i:s A') }}</div>
@endsection
