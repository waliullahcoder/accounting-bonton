@extends('layouts.admin.app')

@section('content')
    <div class="card">
        <div class="card-header pe-2 py-2">
            <div class="d-flex flex-wrap justify-content-between gap-2 align-items-center">
                <h6 class="h6 mb-0 text-uppercase text-nowrap flex-grow-1">
                    {{ isset($title) ? $title : 'Please Set Title' }}</h6>
                <a href="{{ Route('admin.automation-approve.index') }}" class="btn btn-primary btn-sm">Go
                    Back</a>
            </div>
        </div>
        <div class="card-body px-3">
            <table class="table table-borderless table-striped table-responsive-sm">
                <tbody>
                    <tr>
                        <th width="200">Company Name</th>
                        <th width="10">:</th>
                        <td>{{ @$transaction->company->name }}</td>
                    </tr>
                    <tr>
                        <th width="200">Voucher No.</th>
                        <th width="10">:</th>
                        <td>{{ @$transaction->voucher_no }}</td>
                    </tr>
                    <tr>
                        <th width="200">Transaction Date</th>
                        <th width="10">:</th>
                        <td>{{ date('d-m-Y', strtotime(@$transaction->voucher_date)) }}</td>
                    </tr>
                    <tr>
                        <th width="200">Remarks</th>
                        <th width="10">:</th>
                        <td>{{ @$transaction->narration }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="table table-bordered table-striped table-responsive-sm">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="text-center" width="60">SL#</th>
                        <th>Account Name</th>
                        <th width="200">Debit</th>
                        <th width="200">Credit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions as $item)
                        <tr>
                            <th class="text-center">{{ $loop->iteration }}</th>
                            <th>{{ $item->coa->head_name }} - {{ $item->coa->head_code }}</th>
                            <td>{{ number_format($item->debit_amount, 2, '.', ',') }}</td>
                            <td>{{ number_format($item->credit_amount, 2, '.', ',') }}</td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        <th colspan="2" class="text-end">Total Amount</th>
                        <th>{{ number_format($transactions->sum('debit_amount'), 2, '.', ',') }}</th>
                        <th>{{ number_format($transactions->sum('credit_amount'), 2, '.', ',') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection
