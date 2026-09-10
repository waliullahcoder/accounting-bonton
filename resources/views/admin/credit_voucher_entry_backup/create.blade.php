@extends('layouts.admin.create_app')

@section('content')
    <div class="row g-3">
        <div class="col-lg-4 col-sm-6">
            <label for="debit_head" class="form-label"><b>Debit Account Head <span class="text-danger">*</span></b></label>
            <select name="debit_head" id="debit_head" class="form-select select" data-placeholder="select Account Name"
                required>
                <option value="">Select Account Name</option>
                @foreach ($debitCoas as $item)
                    <option value="{{ $item->id }}">{{ $item->head_name }} - {{ $item->head_code }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="document" class="form-label"><b>Document</b></label>
            <input type="file" class="form-control" name="document" id="document" accept="image/*, .pdf">
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="voucher_date" class="form-label"><b>Transaction Date <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control date_picker" name="voucher_date" id="voucher_date"
                placeholder="Transaction Date" required>
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
            <label for="narration" class="form-label"><b>Remarks</b></label>
            <textarea class="form-control narration" id="narration" name="narration" rows="1" spellcheck="false"
                placeholder="Remarks"></textarea>
        </div>
        <div class="col-lg-4 col-sm-6">
            <label class="form-label text-white"><b>Add Account</b></label>
            <button type="button" class="btn btn-xs btn-primary w-100 px-2 py-2" id="add_item">Add Account</button>
        </div>
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped target-table align-middle mb-0">
                    <thead class="bg-primary border-primary text-white">
                        <tr>
                            <th class="py-1 text-center" width="50">SL#</th>
                            <th class="py-1 px-3">Account Name</th>
                            <th class="py-1 text-end" width="200">Credit</th>
                            <th class="py-1 text-center" width="100">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                    </tbody>
                    <tfoot class="bg-primary text-white align-middle border-primary">
                        <tr>
                            <th colspan="2" class="py-1 text-end">Total Amount</th>
                            <td class="py-1">
                                <input type="number" step="any" class="totalCredit text-end border-transparent"
                                    id="totalCredit" name="totalCredit" value="0" readonly>
                            </td>
                            <th class="py-1"></th>
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
            $(".date_picker").datepicker({
                format: 'dd-mm-yyyy',
                changeMonth: true,
                changeYear: true,
            }).datepicker('setDate', 'today');

            $(document).on('click', '#add_item', function(e) {
                e.preventDefault();
                var head_id = $('#credit_heads').val();
                if (head_id == '') {
                    Swal.fire({
                        width: "22rem",
                        toast: true,
                        position: 'top-right',
                        text: "Please select a credit head!",
                        icon: "error",
                        showConfirmButton: false,
                        timer: 1500
                    });
                    return;
                }
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
                            <input type="number" class="credit text-end" name="credit_amount[${head_id}]" oninput="findTotal()"
                                value="0">
                        </td>
                        <td class="py-1 text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm w-100 remove_btn">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>`;
                $('#tbody').append(tr);
                $('#credit_heads option[value=' + head_id + ']').remove();
                $('#credit_heads').trigger("chosen:updated");
            });

            $(document).on('click', '.remove_btn', function(e) {
                e.preventDefault();
                $(this).closest('tr').remove();
                $('.serial').each(function(index, value) {
                    $(value).text(index + 1);
                });
                var coa_id = $(this).closest('tr').find('.coa_id').val();
                var head_name = $(this).closest('tr').find('.head_name').text();
                var head_code = $(this).closest('tr').find('.head_code').val();
                var option =
                    `<option value="${ coa_id }" data-name="${head_name}" data-code="${head_code}">${head_name} - ${head_code}</option>`;
                $('#credit_heads').append(option);
                $('#credit_heads').trigger("chosen:updated");
                findTotal();
            });
        });

        function findTotal() {
            var totalCredit = 0;
            var remarks = '';
            var loop = $('.row-count').val();
            $(".credit").each(function(index, value) {
                var credit = parseInt($(this).val());
                totalCredit += isNaN(credit) ? 0 : credit;
                var credit_head = $('.head_name')[index];
                var head_name = $(credit_head).text();
                var code = $('.head_code')[index];
                var head_code = $(code).val();
                if (remarks != '') {
                    remarks += ', ';
                }
                remarks += head_name;
            });
            $('#narration').val(remarks);
            $('#totalCredit').val(totalCredit);
        }
    </script>
@endpush
