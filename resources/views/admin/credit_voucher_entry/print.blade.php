@extends('layouts.admin.print_app')

@section('content')
    <div class="content-wrapper">
        <table class="table mb-3 info-table" style="border-bottom: 1px solid #ddd;">
            <tr>
                <td>
                    <b class="d-inline-block" style="min-width: 120px;">Debit Account Head :</b>
                    <span class="d-inline-block" style="min-width: 200px;">{{ @$data->coa->head_name }} - {{ @$data->coa->head_code }}</span>
                </td>
                <td class="text-right">
                    <b class="d-inline-block text-left">Date :</b>
                    <span class="d-inline-block"
                        style="min-width: 110px;">{{ date('d-m-Y', strtotime(@$data->date)) }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <b class="d-inline-block" style="min-width: 120px;">Voucher No :</b>
                    <span class="d-inline-block" style="min-width: 200px;">{{ @$data->voucher_no }}</span>
                </td>
                <td class="text-right">
                    <b class="d-inline-block text-left">Remarks :</b>
                    <span class="d-inline-block"
                        style="min-width: 110px;">{{ @$data->narration }}</span>
                </td>
            </tr>
        </table>
        <table class="table table-bordered table-condensed table-striped align-middle mb-3">
            <thead>
                <tr>
                    <th class="text-center" width="20">SL#</th>
                    <th>Account Name</th>
                    <th class="text-right" width="200">Credit</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($creditEntries as $item)
                    <tr>
                        <th class="text-center">{{ $loop->iteration }}</th>
                        <th>{{ $item->coa->head_name }} - {{ $item->coa->head_code }}</th>
                        <td class="text-right">{{ number_format($item->credit_amount, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"><b>In words :</b>
                        {{ \App\HelperClass::convertNumber($creditEntries->sum('credit_amount')) }} Taka Only
                    </td>
                    <td class="text-right">
                        {{ number_format($creditEntries->sum('credit_amount'), 2) }}</td>
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
