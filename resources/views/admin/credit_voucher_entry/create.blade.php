@extends('layouts.admin.create_app')

@section('content')
    <div class="row g-3">
        <div class="col-lg-4 col-sm-6">
            <label for="project_id" class="form-label"><b>Project Name</b></label>
            <select name="project_id" id="project_id" class="form-select select" data-placeholder="select Project Name">
                <option value=""></option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="debit_head" class="form-label"><b>Debit Account Head <span class="text-danger">*</span></b></label>
            <select name="debit_head" id="debit_head" class="form-select select" data-placeholder="select Account Name"
                required>
                <option value=""></option>
                @foreach ($debitCoas as $item)
                    <option value="{{ $item->id }}" {{ old('debit_head') == $item->id ? 'selected' : '' }}>
                        {{ $item->head_name }} - {{ $item->head_code }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="document" class="form-label"><b>Document</b></label>
            <input type="file" class="form-control" name="document" id="document" accept="image/*,application/pdf">
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="voucher_date" class="form-label"><b>Date <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control date_picker" name="voucher_date" id="voucher_date" placeholder="Date"
                value="{{ date('d-m-Y', strtotime(old('voucher_date', date('d-m-Y')))) }}" required>
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="credit_heads" class="form-label"><b>Credit Account Head <span
                        class="text-danger">*</span></b></label>
            <select name="credit_heads" id="credit_heads" class="form-select select" data-placeholder="select Credit Head">
                <option value="">Select Account Name</option>
                @foreach ($coas as $item)
                    <option value="{{ $item->id }}" data-name="{{ $item->head_name }}"
                        data-code="{{ $item->head_code }}">{{ $item->head_name }} -
                        {{ $item->head_code }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-4 col-sm-6">
            <label for="narration" class="form-label text-white"><b>Add Account</b></label>
            <button type="button" class="btn btn-xs btn-primary w-100 px-2 py-2" id="add_item">Add Account</button>
        </div>
        <div class="col-lg-12 col-sm-12">
            <label for="narration" class="form-label"><b>Remarks</b></label>
            <textarea class="form-control narration" id="narration" name="narration" rows="1" spellcheck="false"
                placeholder="Remarks">{{ old('narration') }}</textarea>
        </div>
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped target-table align-middle mb-0">
                    <thead class="bg-primary border-primary text-white text-nowrap">
                        <tr>
                            <th class="py-1 text-center" width="50">SL#</th>
                            <th class="py-1 px-3">Account Name</th>
                            <th class="py-1 text-end" width="200">Credit</th>
                            <th class="py-1 text-center" width="100">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                    </tbody>
                    <tfoot class="bg-primary align-middle border-primary">
                        <tr>
                            <th colspan="2" class="py-1 text-end text-white">Total Amount</th>
                            <td class="py-1">
                                <input type="number" step="any" class="totalCredit text-end form-control input-sm"
                                    id="totalCredit" name="totalCredit" value="0" readonly>
                            </td>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            $(document).on('click', '#add_item', function(e) {
                e.preventDefault();
                var head_id = $('#credit_heads').val();
                if (head_id) {
                    var head_name = $('#credit_heads option:selected').data('name');
                    var head_code = $('#credit_heads option:selected').data('code');
                    var count_rows = $('#tbody tr').length;
                    var tr = `
                        <tr>
                            <th class="py-1 text-center serial">${count_rows+1}</th>
                            <td class="py-1 px-3">
                                <input type="hidden" name="coa_id[]" class="coa_id" value="${head_id}">
                                <input type="hidden" name="head_code[${head_id}]" class="head_code" value="${head_code}">
                                <b class="head_name">${head_name} - ${head_code}</b>
                            </td>
                            <td class="py-1">
                                <input type="number" class="credit text-end form-control input-sm" name="credit_amount[${head_id}]" oninput="calculate()"
                                    value="0">
                            </td>
                            <td class="py-1 text-center">
                                <button type="button" class="btn btn-outline-danger btn-sm w-100 remove_btn">
                                    <i class="far fa-trash-alt pb-1"></i>
                                </button>
                            </td>
                        </tr>`;
                    $('#tbody').append(tr);
                    $('#credit_heads option[value=' + head_id + ']').remove();
                }
            });

            $(document).on('click', '.remove_btn', function(e) {
                e.preventDefault();
                $(this).closest('tr').remove();
                $('.serial').each(function(index, value) {
                    $(value).text(index + 1);
                });
                var coa_id = $(this).closest('tr').find('.coa_id').val();
                var headName = $(this).closest('tr').find('.head_name').text();
                var headCode = $(this).closest('tr').find('.head_code').val();
                var option =
                    `<option value="${ coa_id }" data-name="${headName}" data-code="${headCode}">${headName}</option>`;
                $('#credit_heads').append(option);
                calculate();
            });

            $(document).on('submit', '#store_form', function(e) {
                var totalAmount = $('#totalCredit').val();
                if (totalAmount <= 0) {
                    e.preventDefault();
                    Swal.fire({
                        width: "26rem",
                        toast: true,
                        position: 'top-right',
                        text: 'Total Amount must be greater than 0.',
                        icon: "error",
                        showConfirmButton: false,
                        timer: 1500,
                        showClass: {
                            popup: `
                                animate__animated
                                animate__bounceInRight
                                animate__faster
                            `
                        },
                        hideClass: {
                            popup: `
                                animate__animated
                                animate__bounceOutRight
                                animate__faster
                            `
                        }
                    });
                }
            });
        });

        function calculate() {
            let totalCredit = 0;
            const remarks = [];

            $('.credit').each(function(index) {
                const credit = parseFloat($(this).val()) || 0;
                totalCredit += credit;

                const headName = $('.head_name').eq(index).text();
                if (headName) remarks.push(headName);
            });

            $('#narration').val(remarks.join(', '));
            $('#totalCredit').val(totalCredit);
        }
    </script>
@endpush
