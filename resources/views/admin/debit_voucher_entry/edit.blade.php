@extends('layouts.admin.edit_app')

@section('content')
    <div class="row g-3">
        <div class="col-lg-4 col-sm-6">
            <label for="project_id" class="form-label"><b>Project Name</b></label>
            <select name="project_id" id="project_id" class="form-select select" data-placeholder="select Project Name">
                <option value=""></option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" {{ $project->id == $data->project_id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="credit_head" class="form-label"><b>Credit Account Head <span
                        class="text-danger">*</span></b></label>
            <select name="credit_head" id="credit_head" class="form-select select" data-placeholder="select Account Name"
                required>
                <option value="">Select Account Name</option>
                @foreach ($creditCoas as $item)
                    <option value="{{ $item->id }}" {{ $data->coa_setup_id == $item->id ? 'selected' : '' }}>
                        {{ $item->head_name }} - {{ $item->head_code }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="document" class="form-label"><b>Document @if (file_exists($data->document))
                        <a href="{{ asset($data->document) }}" class="text-danger" target="_blank">Download</a>
                    @endif
                </b></label>
            <input type="file" class="form-control" name="document" id="document" accept="image/*, .pdf">
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="voucher_date" class="form-label"><b>Transaction Date <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control date_picker" name="voucher_date" id="voucher_date"
                value="{{ date('d-m-Y', strtotime(old('voucher_date') ?? $data->voucher_date)) }}"
                placeholder="Transaction Date" required>
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="debit_heads" class="form-label"><b>Debit Account Head <span class="text-danger">*</span></b></label>
            <select name="debit_heads" id="debit_heads" class="form-select select" data-placeholder="select Debit Head">
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
                placeholder="Remarks">{{ $data->narration }}</textarea>
        </div>

        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-bordered table-striped target-table align-middle mb-0">
                    <thead class="bg-primary border-primary text-white">
                        <tr>
                            <th class="py-1 text-center" width="50">SL#</th>
                            <th class="py-1 px-3">Account Name</th>
                            <th class="py-1 text-end" width="200">Debit</th>
                            <th class="py-1 text-center" width="100">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">
                        @foreach ($debitEntries as $item)
                            <tr>
                                <th class="py-1 text-center serial">{{ $loop->iteration }}</th>
                                <td class="py-1 px-3">
                                    <input type="hidden" name="coa_id[]" class="coa_id" value="{{ $item->coa_setup_id }}">
                                    <input type="hidden" name="head_code[{{ $item->coa_setup_id }}]" class="head_code"
                                        value="{{ @$item->coa->head_code }}">
                                    <b class="head_name">{{ @$item->coa->head_name }} - {{ @$item->coa->head_code }}</b>
                                </td>
                                <td class="py-1">
                                    <input type="number" class="debit text-end"
                                        name="debit_amount[{{ $item->coa_setup_id }}]" oninput="findTotal()"
                                        value="{{ $item->debit_amount }}">
                                </td>
                                <td class="py-1 text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100 remove_btn">
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-primary text-white align-middle border-primary">
                        <tr>
                            <th colspan="2" class="py-1 text-end">Total Amount</th>
                            <td class="py-1">
                                <input type="number" step="any" class="totalDebit text-end border-transparent"
                                    id="totalDebit" name="totalDebit" value="{{ $debitEntries->sum('debit_amount') }}"
                                    readonly>
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
            });

            $(document).on('click', '#add_item', function(e) {
                e.preventDefault();
                var head_id = $('#debit_heads').val();
                var head_name = $('#debit_heads option:selected').data('name');
                var head_code = $('#debit_heads option:selected').data('code');
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
                            <input type="number" class="debit text-end" name="debit_amount[${head_id}]" oninput="findTotal()"
                                value="0">
                        </td>
                        <td class="py-1 text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm w-100 remove_btn">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </td>
                    </tr>`;
                $('#tbody').append(tr);
                $('#debit_heads option[value=' + head_id + ']').remove();
                $('#debit_heads').trigger("chosen:updated");
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
                $('#debit_heads').append(option);
                $('#debit_heads').trigger("chosen:updated");
                findTotal();
            });
        });

        function findTotal() {
            var totalDebit = 0;
            var remarks = '';
            var loop = $('.row-count').val();
            $(".debit").each(function(index, value) {
                var debit = parseInt($(this).val());
                totalDebit += isNaN(debit) ? 0 : debit;
                var debit_head = $('.head_name')[index];
                var head_name = $(debit_head).text();
                var code = $('.head_code')[index];
                var head_code = $(code).val();
                if (remarks != '') {
                    remarks += ', ';
                }
                remarks += head_name;
            });
            $('#narration').val(remarks);
            $('#totalDebit').val(totalDebit);
        }
    </script>
@endpush
