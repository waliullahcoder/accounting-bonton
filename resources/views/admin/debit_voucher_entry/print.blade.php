@extends('layouts.admin.print_app')

@section('content')
    <div class="content-wrapper">
        <table class="table mb-3 info-table" style="border-bottom: 1px solid #ddd;">
            <tr>
                <td>
                    <b class="d-inline-block" style="min-width: 120px;">Voucher No :</b>
                    <span class="d-inline-block" style="min-width: 200px;">{{ @$creditEntry->voucher_no }}</span>
                </td>
                <td class="text-right">
                    <b class="d-inline-block text-left">Date :</b>
                    <span class="d-inline-block"
                        style="min-width: 110px;">{{ date('d-m-Y', strtotime(@$creditEntry->voucher_date)) }}</span>
                </td>
            </tr>
        </table>
        <table class="table table-bordered table-condensed table-striped align-middle mb-3">
            <thead>
                <tr>
                    <th class="text-center" width="60">SL#</th>
                    <th>Account Name</th>
                    <th class="text-right" width="200">Debit</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($debitEntries as $item)
                    <tr>
                        <th class="text-center">{{ $loop->iteration }}</th>
                        <th>{{ $item->coa->head_name }} - {{ $item->coa->head_code }}</th>
                        <td class="text-right">{{ number_format($item->debit_amount, 2, '.', ',') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"><b>In words :</b>
                        {{ \App\HelperClass::convertNumber($debitEntries->sum('debit_amount')) }} Taka
                        Only
                    </td>
                    <td class="text-right">
                        {{ number_format($debitEntries->sum('debit_amount'), 2, '.', ',') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div>
        <div class="signature-area">
            <div class="signature-item">
                <span>Receive From</span>
            </div>
            <div class="signature-item">
                <span>Accountant</span>
            </div>
        </div>
    </div>
@endsection
