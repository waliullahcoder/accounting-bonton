@extends('layouts.admin.report_app')

@section('form')
    <div class="row g-3">
        <input type="hidden" name="print" value="">
        <input type="hidden" name="filter" value="1">
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
    <div class="table-responsive">
        <table id="dataTable" class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th class="text-center" width="20px">Sl#</th>
                    <th>General Ledger Head</th>
                    <th class="text-end">Debit Amount</th>
                    <th class="text-end">Credit Amount</th>
                </tr>
            </thead>
            @php
                $sl = 1;
            @endphp
            <tbody>
                @foreach ($coaLists as $row)
                    <tr>
                        <td class="text-center px-3">{{ $sl++ }}</td>
                        <td class="px-3">{{ $row->coa_setup->head_name }} - {{ $row->coa_setup->head_code }}</td>
                        <td class="text-end px-3">{{ number_format($row->debit_amount, 2, '.', ',') }}</td>
                        <td class="text-end px-3">{{ number_format($row->credit_amount, 2, '.', ',') }}</td>
                    </tr>
                @endforeach
                @foreach ($coaLists1 as $row)
                    <tr>
                        <td class="text-center px-3">{{ $sl++ }}</td>
                        <td class="px-3">{{ $row->parent_head->head_name }} - {{ $row->parent_head->head_code }}</td>
                        <td class="text-end px-3">{{ number_format($row->debit_amount, 2, '.', ',') }}</td>
                        <td class="text-end px-3">{{ number_format($row->credit_amount, 2, '.', ',') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-primary">
                    <th class="text-end text-white px-3" colspan="2">Total Summary</th>
                    <th class="text-end text-white px-3">
                        {{ number_format((count($coaLists) > 0 ? $coaLists->sum('debit_amount') : 0) + (count($coaLists1) > 0 ? $coaLists1->sum('debit_amount') : 0), 2, '.', ',') }}
                    </th>
                    <th class="text-end text-white px-3">
                        {{ number_format((count($coaLists) > 0 ? $coaLists->sum('credit_amount') : 0) + (count($coaLists1) > 0 ? $coaLists1->sum('credit_amount') : 0), 2, '.', ',') }}
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "order": false,
                dom: 'Bfrtip',
                responsive: true,
                buttons: [
                    'excelHtml5',
                    {
                        'text': '<i class="fal fa-file-pdf"></i> Print',
                        'className': 'getPdf',
                    },
                ]
            });

            $(document).on('click', '.getPdf', function(e) {
                e.preventDefault();
                $('input[name="print"]').val('true');
                $('.filter_form')[0].setAttribute("target", "_blank");
                $('.filter_form').submit();
            });

            $(document).on('click', '#filter_btn', function(e) {
                $('input[name="print"]').val('');
                $('.filter_form')[0].setAttribute("target", "_self");
            });
        });
    </script>
@endpush
