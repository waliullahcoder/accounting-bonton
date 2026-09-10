@extends('layouts.admin.create_app')

@section('content')
    <input type="hidden" name="generate" value="1">
    <div class="row g-3">
        @php
            $data = \App\Models\ClientCollection::where('year', date('Y'))->where('month', date('F'))->first();
        @endphp
        <div class="col-lg-3 col-sm-6">
            <label for="project_id" class="form-label"><b>Project Name <span class="text-danger">*</span></b></label>
            <select name="project_id" id="project_id" class="form-select select" data-placeholder="select Project Name"
                required>
               <option value="">Select Project Name</option>
               @foreach($projects as $project)
               <option value="{{$project->id}}">{{$project->name}}</option>
               @endforeach
            </select>
        </div>
        <div class="col-lg-3 col-sm-6">
            <label for="date" class="form-label"><b>Date <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control date_picker" id="date" name="date"
                value="{{ old('date') ? date('d-m-Y', strtotime(old('date'))) : date('d-m-Y') }}" placeholder="Date"
                required>
        </div>
        <div class="col-lg-3 col-sm-6">
            <label for="month" class="form-label"><b>Month <span class="text-danger">*</span></b></label>
            <select class="form-select select" name="month" id="month" data-placeholder="Select Month" required>
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ date('F', mktime(0, 0, 0, $i, 1)) }}"
                        {{ date('F', mktime(0, 0, 0, $i, 1)) == date('F') ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="col-lg-3 col-sm-6">
            <label for="year" class="form-label"><b>Year <span class="text-danger">*</span></b></label>
            <select class="form-select select" name="year" id="year" data-placeholder="Select Year" required>
                @for ($i = 2015; $i <= 2055; $i++)
                    <option value="{{ $i }}" {{ date('Y') == $i ? 'selected' : '' }}>
                        {{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="col-lg-3 col-sm-6">
            <label for="serial_no" class="form-label"><b>Serial No <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control" id="serial_no" name="serial_no"
                value="{{ @$data->serial_no ?? $serial_no }}" placeholder="Serial No." readonly required>
        </div>
        <div class="col-12" id="response">
            @include('admin.client-collection.partial.table')
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            if ($('.service_id:checked').length == $('.service_id').length) {
                $('#checkAll').prop('checked', true);
            } else {
                $('#checkAll').prop('checked', false);
            }

            $(document).on('click', '#checkAll', function(e) {
                if ($(this).is(':checked')) {
                    $('.service_id').prop('checked', true);
                } else {
                    $('.service_id').prop('checked', false);
                }
                calculate();
            });

            $(document).on('keyup', '.collection_amount', function(e) {
                var amount = +$(this).val();
                var service_id = $(this).data('id');
                if (amount > 0) {
                    $('#check_' + service_id).prop('checked', true);
                } else {
                    $('#check_' + service_id).prop('checked', false);
                }
                calculate();
            });

            $(document).on('click', '.service_id', function(e) {
                calculate();
            });

            function calculate() {
                var collection_amount = 0;
                $('.service_id:checked').each(function(index, value) {
                    var service_id = $(this).val();
                    var bill = +$('#bill_amount' + service_id).val();
                    var collection = +$('#collection_amount' + service_id).val();
                    $('#balance_amount' + service_id).val(bill - collection);
                    collection_amount += collection;
                });
                $('#total_collection_amount').val(collection_amount);

                if ($('.service_id:checked').length == $('.service_id').length) {
                    $('#checkAll').prop('checked', true);
                } else {
                    $('#checkAll').prop('checked', false);
                }
            }

            $(document).on('change', '#month,#year', function(e) {
                var month = $('#month').val();
                var year = $('#year').val();
                $.ajax({
                    url: '{{ request()->fullUrl() }}',
                    data: {
                        month: month,
                        year: year
                    },
                    success: function(response) {
                        $('#response').html(response.data);
                        $('#serial_no').val(response.serial_no);
                        if ($('.service_id:checked').length == $('.service_id').length) {
                            $('#checkAll').prop('checked', true);
                        } else {
                            $('#checkAll').prop('checked', false);
                        }
                    }
                });
            })
        });
    </script>
@endpush
