@extends('layouts.admin.app')

@section('content')
    <div class="card">
        <div class="card-header pe-2 py-2">
            <div class="d-flex align-items-center justify-content-between gap-2">
                <h6 class="h6 mb-0 text-uppercase py-1">
                    {{ isset($title) ? $title : 'Please Set Title' }}
                </h6>
                <form method="get" action="{{ Route('admin.head-details.index') }}">
                    <input type="hidden" name="id" value="{{ request('id') }}">
                    <input type="hidden" name="details_print" value="1">
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
                                    <td>
                                        <a href="{{ Route('admin.head-details.index') }}?coa_setup_id={{ $row->coa_setup_id }}&balance_sheet=1"
                                            target="_blank">
                                            {{ @$row->coa->head_name }}
                                        </a>
                                    </td>
                                    <td>{{ @$row->coa->head_code }}</td>
                                    <td class="text-end">{{ $row->debit_amount }}</td>
                                    <td class="text-end">{{ $row->credit_amount }}</td>
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
                                <th colspan="3" class="text-end">Total Summary</th>
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
