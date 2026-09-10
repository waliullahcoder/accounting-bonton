@extends('layouts.admin.report_app')

@section('form')
    <input type="hidden" name="print" value="">
    <div class="row g-3">
        <div class="col-sm-6">
            <label for="head_type" class="form-label"><b>Head Type</b></label>
            <select name="head_type" id="head_type" class="form-select select" data-placeholder="Select Head Type">
                <option value=""></option>
                <option value="gl">Transaction head under GL</option>
                <option value="mother">Transaction head under Mother</option>
            </select>
        </div>
        <div class="col-sm-6">
            <label for="parent_head" class="form-label"><b>Parent Head</b></label>
            <select name="parent_head" id="parent_head" class="form-select select" data-placeholder="Select Parent Head">
                <option value=""></option>
            </select>
        </div>
    </div>
@endsection

@section('content')
    {!! $dataTable->table() !!}
@endsection

@push('js')
    <script type="text/javascript" src="{{ asset('vendor/datatables/buttons.server-side.js') }}"></script>
    {!! $dataTable->scripts() !!}

    <script type="text/javascript">
        $(document).ready(function() {
            const table = $('#dataTable');
            table.on('preXhr.dt', function(e, settings, data) {
                data.parent_head = $('#parent_head').val();
            });

            $(document).on('change', '#head_type', function() {
                let head_type = $(this).val();
                let url = "{{ url()->current() }}";
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _method: 'GET',
                        getHeads: 'true',
                        head_type: head_type,
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $('#parent_head option').remove();
                            $('#parent_head').append('<option value=""></option>');
                            $.each(response.heads, function(key, value) {
                                var html =
                                    `<option value="${value.head_code}">${value.head_name} - ${value.head_code}</option>`;
                                $('#parent_head').append(html);
                            });
                        }
                    }
                });
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
