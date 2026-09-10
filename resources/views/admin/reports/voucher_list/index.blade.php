@extends('layouts.admin.report_app')

@section('form')
    <input type="hidden" name="print" value="">
    <div class="row g-3">
        <div class="col-sm-4">
            <label for="project_id" class="form-label"><b>Project Name</b></label>
            <select name="project_id" id="project_id" class="form-select select" data-placeholder="select Project Name">
                <option value="">Select Project Name</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-4">
            <label for="voucher_type" class="form-label"><b>Voucher Type</b></label>
            <select name="voucher_type" id="voucher_type" class="form-select select" data-placeholder="Select Voucher Type">
                <option value=""></option>
                @foreach ($voucher_types as $item)
                    <option value="{{ $item->voucher_type }}">{{ $item->voucher_type }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-4">
            <label for="date_range" class="form-label"><b>Date</b></label>
            <input type="text" class="form-control date-range" name="date_range" id="date_range"
                placeholder="Select Date Range" data-time-picker="true" data-format="DD-MM-Y"
                data-separator=" to " autocomplete="off" required value="{{ date('01-m-Y') . ' to ' . date('t-m-Y') }}">
        </div>
    </div>
@endsection

@section('content')
    {!! $dataTable->table(['class' => 'dataTable table align-middle table-bordered'], true) !!}
@endsection

@push('js')
    <script type="text/javascript" src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    {!! $dataTable->scripts() !!}

    <script type="text/javascript">
        $(document).ready(function() {
            const table = $('#dataTable');
            table.on('preXhr.dt', function(e, settings, data) {
                data.date_range = $('#date_range').val();
                data.voucher_type = $('#voucher_type').val();
                data.project_id = $('#project_id').val();
            });

            $(document).on('click', '.getPdf', function(e) {
                e.preventDefault();
                $('input[name="print"]').val('true');
                $('.filter_form')[0].setAttribute("target", "_blank");
                $('.filter_form').submit();
            });

            $(document).on('click', '#filter_btn', function(e) {
                e.preventDefault();
                $('input[name="print"]').val('');
                $('.filter_form')[0].setAttribute("target", "_self");
                $('.dataTable').DataTable().ajax.reload();
            });
        });
    </script>
@endpush
