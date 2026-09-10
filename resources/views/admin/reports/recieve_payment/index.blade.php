@extends('layouts.admin.report_app')

@section('form')
    <input type="hidden" name="print" value="">
    <input type="hidden" name="filter" value="1">
    <div class="row g-3">
        <div class="col-6">
            <label for="project_id" class="form-label"><b>Project Name</b></label>
            <select name="project_id" id="project_id" class="form-select select" data-placeholder="select Project Name">
                <option value=""></option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6">
            <label for="date_range" class="form-label"><b>Date</b></label>
            <input type="text" class="form-control date-range" name="date_range" id="date_range"
                placeholder="Select Date Range" data-time-picker="true" data-format="DD-MM-Y" data-separator=" to "
                autocomplete="off" required
                value="{{ date('d-m-Y', strtotime($start_date)) . ' to ' . date('d-m-Y', strtotime($end_date)) }}">
        </div>
    </div>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-md-6">
            <div class="row g-4">
                <div class="col-12">
                    <table class="table table-bordered table-sm mb-0">
                        <thead>
                            <tr class="bg-primary text-white">
                                <th colspan="4" class="text-center">Receives</th>
                            </tr>
                            <tr>
                                <th class="text-center" width="60">SL#</th>
                                <th>Head Code</th>
                                <th>Head Name</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $row)
                                <tr>
                                    <th class="text-center">{{ $loop->iteration }}</th>
                                    <td>
                                        <a
                                            href="{{ Route('admin.receive-payment-head-details.index') }}?coa_setup_id={{ $row->coa_setup_id }}&start_date={{ $start_date }}&end_date={{ $end_date }}">
                                            {{ $row->coa->head_code }}
                                        </a>
                                    </td>
                                    <td>
                                        <a
                                            href="{{ Route('admin.receive-payment-head-details.index') }}?coa_setup_id={{ $row->coa_setup_id }}&start_date={{ $start_date }}&end_date={{ $end_date }}">
                                            {{ $row->coa->head_name }}
                                        </a>
                                    </td>
                                    <td class="text-end">
                                        <a
                                            href="{{ Route('admin.receive-payment-head-details.index') }}?coa_setup_id={{ $row->coa_setup_id }}&start_date={{ $start_date }}&end_date={{ $end_date }}">
                                            {{ number_format($row->debit_amount) }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">Total</th>
                                <th class="text-end">{{ number_format(count($data) > 0 ? $data->sum('debit_amount') : 0) }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <table class="table table-bordered table-sm mb-0">
                <thead>
                    <tr class="bg-primary text-white">
                        <th colspan="4" class="text-center">Payments</th>
                    </tr>
                    <tr>
                        <th class="text-center" width="60">SL#</th>
                        <th>Head Code</th>
                        <th>Head Name</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $row)
                        <tr>
                            <th class="text-center">{{ $loop->iteration }}</th>
                            <td>
                                <a
                                    href="{{ Route('admin.receive-payment-head-details.index') }}?coa_setup_id={{ $row->coa_setup_id }}&start_date={{ $start_date }}&end_date={{ $end_date }}">
                                    {{ $row->coa->head_code }}
                                </a>
                            </td>
                            <td>
                                <a
                                    href="{{ Route('admin.receive-payment-head-details.index') }}?coa_setup_id={{ $row->coa_setup_id }}&start_date={{ $start_date }}&end_date={{ $end_date }}">
                                    {{ $row->coa->head_name }}
                                </a>
                            </td>
                            <td class="text-end">
                                <a
                                    href="{{ Route('admin.receive-payment-head-details.index') }}?coa_setup_id={{ $row->coa_setup_id }}&start_date={{ $start_date }}&end_date={{ $end_date }}">
                                    {{ number_format($row->credit_amount) }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total</th>
                        <th class="text-end">{{ number_format(count($data) > 0 ? $data->sum('credit_amount') : 0) }}
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="col-12">
            @if (count($data) > 0)
                @if ($data->sum('debit_amount') > $data->sum('credit_amount'))
                    <h5 class="text-white bg-success text-center mb-0 py-2">Net Balance:
                        {{ number_format($data->sum('debit_amount') - $data->sum('credit_amount')) }}
                    </h5>
                @elseif($data->sum('debit_amount') < $data->sum('credit_amount'))
                    <h5 class="text-white bg-danger text-center mb-0 py-2">Net Balance:
                        {{ number_format($data->sum('credit_amount') - $data->sum('debit_amount')) }}
                    </h5>
                @endif
            @endif
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('click', '.getPdf', function(e) {
                e.preventDefault();
                $('input[name="print"]').val('true');
                $('.filter_form').submit();
            });

            $(document).on('click', '#filter_btn', function(e) {
                e.preventDefault();
                $('input[name="print"]').val('');
                $('.filter_form').submit();
            });
        });
    </script>
@endpush
