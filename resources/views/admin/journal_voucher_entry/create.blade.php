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
            <label for="document" class="form-label"><b>Document</b></label>
            <input type="file" class="form-control" name="document" id="document" accept="image/*, .pdf">
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="voucher_date" class="form-label"><b>Transaction Date <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control date_picker" name="voucher_date" id="voucher_date"
                placeholder="Transaction Date" required>
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="coa_heads" class="form-label"><b>Account Head <span class="text-danger">*</span></b></label>
            <select name="coa_heads" id="coa_heads" class="form-select select" data-placeholder="select Account Head">
                <option value="">Select Account Name</option>
                @foreach ($coas as $item)
                    <option value="{{ $item->id }}" data-name="{{ $item->head_name }}"
                        data-code="{{ $item->head_code }}">{{ $item->head_name }} -
                        {{ $item->head_code }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-4 col-sm-4">
            <label for="narration" class="form-label"><b>Remarks</b></label>
            <textarea class="form-control narration" id="narration" name="narration" rows="1" spellcheck="false"
                placeholder="Remarks"></textarea>
        </div>
        <div class="col-lg-4 col-sm-4">
            <label class="form-label text-white"><b>add</b></label>
            <button type="button" class="btn btn-xs btn-primary w-100 px-2 py-2" id="add_item">Add Account</button>
        </div>
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped target-table align-middle mb-0">
                    <thead class="bg-primary border-primary text-white">
                        <tr>
                            <th class="py-1 text-center" width="50">SL#</th>
                            <th class="py-1 px-3">Account Name</th>
                            <th class="py-1 text-end" width="200">Debit</th>
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
                                <input type="number" step="any" class="totalDebit text-end border-transparent"
                                    id="totalDebit" name="totalDebit" value="0" readonly>
                            </td>
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
                var head_id = $('#coa_heads').val();
                if (head_id == '') {
                    Swal.fire({
                        width: "24rem",
                        toast: true,
                        position: 'top-right',
                        text: "Please select a account head!",
                        icon: "error",
                        showConfirmButton: false,
                        timer: 1500
                    });
                    return;
                }
                var head_name = $('#coa_heads option:selected').data('name');
                var head_code = $('#coa_heads option:selected').data('code');
                var count_rows = $('#tbody tr').length;
                var tr = `
                    <tr>
                        <th class="py-1 text-center serial">${count_rows+1}</th>
                        <td class="py-1">
                            <input type="hidden" name="coa_id[]" class="coa_id" value="${head_id}">
                            <input type="hidden" name="head_code[${head_id}]" class="head_code" value="${head_code}">
                            <input type="text" class="head_name w-100" name="head_name[${head_id}]" readonly
                                value="${head_name} - ${head_code}">
                        </td>
                        <td class="py-1">
                            <input type="number" class="debit text-end" name="debit_amount[${head_id}]" oninput="findTotal()"
                                value="0">
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
                $('#coa_heads option[value=' + head_id + ']').remove();
                $('#coa_heads').trigger("chosen:updated");
            });

            $(document).on('click', '.remove_btn', function(e) {
                e.preventDefault();
                $(this).closest('tr').remove();
                $('.serial').each(function(index, value) {
                    $(value).text(index + 1);
                });
                var coa_id = $(this).closest('tr').find('.coa_id').val();
                var head_name = $(this).closest('tr').find('.head_name').val();
                var head_code = $(this).closest('tr').find('.head_code').val();
                var option =
                    `<option value="${ coa_id }" data-name="${head_name}" data-code="${head_code}">${head_name} - ${head_code}</option>`;
                $('#coa_heads').append(option);
                $('#coa_heads').trigger("chosen:updated");
                findTotal();
            });

            $(document).on('click', '.submit_btn', function(e) {
                e.preventDefault();
                var debit = $('#totalDebit').val();
                var credit = $('#totalCredit').val();

                if (debit <= 0 || credit <= 0) {
                    Swal.fire({
                        width: "24rem",
                        toast: true,
                        position: 'top-right',
                        text: "Debit and Credit will be greater than 0",
                        icon: "error",
                        showConfirmButton: false,
                        timer: 1500
                    });
                }

                if (credit != debit) {
                    Swal.fire({
                        width: "24rem",
                        toast: true,
                        position: 'top-right',
                        text: "Credit and Debit have to same.",
                        icon: "error",
                        showConfirmButton: false,
                        timer: 1500
                    });
                }

                if (debit > 0 && credit > 0 && credit == debit) {
                    $('#store_form').submit();
                }
            });
        });

        function findTotal() {
            var totalCredit = 0;
            var totalDebit = 0;
            var remarks = '';
            var loop = $('.row-count').val();
            $(".credit").each(function(index, value) {
                var credit = parseInt($(this).val());
                totalCredit += isNaN(credit) ? 0 : credit;
                var debit = parseInt($($('.debit')[index]).val());
                totalDebit += isNaN(debit) ? 0 : debit;

                var head_name = $($('.head_name')[index]).val();
                var head_code = $($('.head_code')[index]).val();
                if (remarks != '') {
                    remarks += ', ';
                }
                remarks += head_name;
            });
            $('#narration').val(remarks);
            $('#totalCredit').val(totalCredit);
            $('#totalDebit').val(totalDebit);
        }
    </script>
@endpush
