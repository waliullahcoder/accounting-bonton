@extends('layouts.admin.app')

@section('content')
    <div class="card">
        <div class="card-header pe-2 py-2">
            <div class="d-flex align-items-center justify-content-between gap-2">
                <h6 class="h6 mb-0 text-uppercase py-1">
                    {{ isset($title) ? $title : 'Please Set Title' }}
                </h6>
                <form method="get" action="{{ Route('admin.receive-payment-head-details.index') }}">
                    <input type="hidden" name="coa_setup_id" value="{{ request('coa_setup_id') }}">
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">
                    <a href="{{ Route('admin.receive-payment.index') }}" class="btn btn-sm btn-primary">Go Back</a>
                    <button type="submit" class="btn btn-sm btn-primary" name="print" value="print">Print</button>
                </form>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12">
                    <table class="table table-bordered mb-0" id="dataTable">
                        <thead>
                            <tr>
                                <th class="text-center" width="30">SL#</th>
                                <th>Voucher No.</th>
                                <th>Head Name</th>
                                <th>Head Code</th>
                                <th class="text-end">Debit Amount</th>
                                <th class="text-end">Credit Amount</th>
                                <th class="text-end">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total_balance = 0;
                            @endphp
                            @foreach ($data as $row)
                                <tr>
                                    <td class="text-center" width="30">{{ $loop->iteration }}</td>
                                    <td>{{ @$row->voucher_no }}</td>
                                    <td>{{ @$row->coa->head_name }}</td>
                                    <td>{{ @$row->coa->head_code }}</td>
                                    <td class="text-end">{{ number_format($row->debit_amount, 2, '.', ',') }}</td>
                                    <td class="text-end">{{ number_format($row->credit_amount, 2, '.', ',') }}</td>
                                    <td class="text-end">
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
                                <th colspan="4" class="text-end">Total Summary</th>
                                <th class="text-end">{{ number_format($data->sum('debit_amount'), 2, '.', ',') }}</th>
                                <th class="text-end">{{ number_format($data->sum('credit_amount'), 2, '.', ',') }}</th>
                                <th class="text-end">
                                    @if ($total_balance < 0)
                                        ({{ number_format(abs($total_balance), 2, '.', ',') }})
                                    @else
                                        {{ number_format($total_balance, 2, '.', ',') }}
                                    @endif
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "order": false,
                responsive: true,
            });
        });
    </script>
@endpush
