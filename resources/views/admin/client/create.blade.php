@extends('layouts.admin.create_app')

@section('content')
    <div class="row g-3">
        <div class="col-sm-6">
            <label for="type" class="form-label"><b>Service Type <span class="text-danger">*</span></b></label>
            <select class="form-select select" name="type" id="type" data-placeholder="Select Service Type" required>
                <option value=""></option>
                <option value="Domain Hosting" {{ old('type') == 'Domain Hosting' ? 'selected' : '' }}>Domain Hosting
                </option>
                <option value="Digital Marketing" {{ old('type') == 'Digital Marketing' ? 'selected' : '' }}>Digital
                    Marketing</option>
                <option value="Support Service" {{ old('type') == 'Support Service' ? 'selected' : '' }}>Support Service
                </option>
            </select>
        </div>
        <div class="col-sm-6">
            <label for="name" class="form-label"><b>Contact Name <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"
                placeholder="Contact Name" required>
        </div>
        <div class="col-sm-6">
            <label for="company_name" class="form-label"><b>Company Name</b></label>
            <input type="text" class="form-control" id="company_name" name="company_name"
                value="{{ old('company_name') }}" placeholder="Company Name">
        </div>
        <div class="col-sm-6">
            <label for="phone" class="form-label"><b>Contact No.</b></label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}"
                placeholder="Contact No.">
        </div>
        <div class="col-sm-6">
            <label for="email" class="form-label"><b>Email Address</b></label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"
                placeholder="Email Address">
        </div>
        <div class="col-sm-6">
            <label for="coa_setup_id" class="form-label"><b>Accounting Head <span class="text-danger">*</span></b></label>
            <select name="coa_setup_id" id="coa_setup_id" class="form-select select" data-placeholder="Select Head"
                required>
                <option value=""></option>
            </select>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('change', '#type', function() {
                $('#coa_setup_id option').remove();
                var type = $(this).val();
                $.ajax({
                    url: '{{ request()->fullUrl() }}',
                    type: 'POST',
                    data: {
                        _method: 'GET',
                        type: type,
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#coa_setup_id').append(`<option value=""></option>`);
                            $.each(response.coas, function(key, value) {
                                $('#coa_setup_id').append(
                                    `<option value="${value.id}">${value.head_name} - ${value.head_code}</option>`
                                );
                            });
                        }
                    }
                });
            });
        })
    </script>
@endpush
