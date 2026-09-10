@extends('layouts.admin.edit_app')

@section('content')
    <div class="row g-3">
        <div class="col-sm-6">
            <label for="client_id" class="form-label"><b>Client <span class="text-danger">*</span></b></label>
            <select class="form-select select" name="client_id" id="client_id" data-placeholder="Select Client" required>
                <option value=""></option>
                @foreach ($additionalData['clients'] as $item)
                    <option value="{{ $item->id }}" {{ $data->client_id == $item->id ? 'selected' : '' }}>
                        {{ $item->company_name }} - {{ $item->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-sm-6">
            <label for="software_type" class="form-label"><b>Software Type <span class="text-danger">*</span></b></label>
            <select class="form-select select" name="software_type" id="software_type"
                data-placeholder="Select Software Type" required>
                <option value=""></option>
                <option value="Apppro ERP" {{ $data->software_type == 'Apppro ERP' ? 'selected' : '' }}>Apppro ERP</option>
                <option value="Sales Tracker" {{ $data->software_type == 'Sales Tracker' ? 'selected' : '' }}>Sales Tracker
                </option>
                <option value="Membership" {{ $data->software_type == 'Membership' ? 'selected' : '' }}>Membership</option>
                <option value="Luckydraw" {{ $data->software_type == 'Luckydraw' ? 'selected' : '' }}>Luckydraw</option>
                <option value="Customized Software" {{ $data->software_type == 'Customized Software' ? 'selected' : '' }}>
                    Customized Software</option>
                <option value="Ecommerce" {{ $data->software_type == 'Ecommerce' ? 'selected' : '' }}>Ecommerce</option>
                <option value="Others" {{ $data->software_type == 'Others' ? 'selected' : '' }}>Others</option>
            </select>
        </div>
        <div class="col-sm-6">
            <label for="pay_type" class="form-label"><b>Pay Type <span class="text-danger">*</span></b></label>
            <select name="pay_type" id="pay_type" class="form-select" data-placeholder="Select Pay Type" required>
                <option value="Monthly" {{ $data->pay_type == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                <option value="Quarterly" {{ $data->pay_type == 'Quarterly' ? 'selected' : '' }}>Quarterly</option>
                <option value="Tri-Yearly" {{ $data->pay_type == 'Tri-Yearly' ? 'selected' : '' }}>Tri-Yearly</option>
                <option value="Half-Yearly" {{ $data->pay_type == 'Half-Yearly' ? 'selected' : '' }}>Half-Yearly</option>
                <option value="Yearly" {{ $data->pay_type == 'Yearly' ? 'selected' : '' }}>Yearly</option>
            </select>
        </div>
        <div class="col-sm-6">
            <label for="month" class="form-label"><b>Months <span class="text-danger">*</span></b></label>
            <select name="month[]" id="month" class="form-select" data-placeholder="Select Months" multiple required>
                @for ($i = 1; $i <= 12; $i++)
                    <option value="{{ date('F', mktime(0, 0, 0, $i, 1)) }}"
                        {{ in_array(date('F', mktime(0, 0, 0, $i, 1)), $data->months->pluck('month')->toArray()) ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="col-sm-6">
            <label for="service_charge" class="form-label"><b>Service Charge <span class="text-danger">*</span></b></label>
            <input type="number" class="form-control" id="service_charge" name="service_charge"
                value="{{ $data->service_charge }}" placeholder="Service Charge" required>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('change', '#pay_type', function() {
                $('#month').prop('selectedIndex', -1);
                var pay_type = $(this).val();
                var limit = 1;
                if (pay_type == 'Monthly') {
                    $('#month option').prop('selected', true);
                    limit = 12;
                } else if (pay_type == 'Quarterly') {
                    limit = 4;
                } else if (pay_type == 'Tri-Yearly') {
                    limit = 3;
                } else if (pay_type == 'Half-Yearly') {
                    limit = 2;
                }
                $('#month').select2({
                    allowClear: true,
                    maximumSelectionLength: limit
                });
            });

            var limit = 1;
            @if ($data->pay_type == 'Monthly')
                var limit = 12;
            @elseif ($data->pay_type == 'Quarterly')
                var limit = 4;
            @elseif ($data->pay_type == 'Tri-Yearly')
                var limit = 3;
            @elseif ($data->pay_type == 'Half-Yearly')
                var limit = 2;
            @endif
            $('#month').select2({
                allowClear: true,
                maximumSelectionLength: limit
            });
        })
    </script>
@endpush
