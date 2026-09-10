<div class="table-responsive">
    <table class="table mb-0">
        <thead>
            <tr class="bg-primary text-white text-nowrap text-uppercase">
                <th class="p-1 text-center" width="50">SL#</th>
                <th class="p-1">Company Name</th>
                <th class="p-1">Software</th>
                <th class="p-1 text-center" width="200">Bill</th>
                <th class="p-1 text-center" width="200">Collection</th>
                <th class="p-1 text-center" width="200">Balance</th>
                <th class="p-1 text-center" width="50">
                    <div class="custom-control custom-checkbox mx-auto">
                        <input type="checkbox" class="custom-control-input" id="checkAll">
                        <label for="checkAll" class="custom-control-label"></label>
                    </div>
                </th>
            </tr>
        </thead>
        <tbody>
            @php
                $total_amount = 0;
                $total_collection = 0;
            @endphp
            @foreach ($billingMonths as $row)
                @php
                    if (!is_null($data)) {
                        $collection_amount = @$data->list->where('service_id', $row->service_id)->first()
                            ->collection_amount;
                    }
                @endphp
                <tr>
                    <td class="text-center p-1">{{ $loop->iteration }}</td>
                    <td class="p-1">{{ @$row->client->company_name }}</td>
                    <td class="p-1">{{ @$row->service->software_type }}</td>
                    <td class="text-center p-1">
                        <input type="number" class="form-control py-1 text-center fs-12"
                            style="min-height: auto; width: 200px;" id="bill_amount{{ @$row->service_id }}"
                            name="bill_amount[{{ @$row->service_id }}]" value="{{ @$row->service->service_charge }}"
                            readonly>
                    </td>
                    <td class="text-center p-1">
                        <input type="number" class="form-control py-1 text-center fs-12 collection_amount"
                            style="min-height: auto; width: 200px;" data-id="{{ @$row->service_id }}"
                            id="collection_amount{{ @$row->service_id }}" max="{{ @$row->service->service_charge }}"
                            name="collection_amount[{{ @$row->service_id }}]" value="{{ @$collection_amount }}">
                    </td>
                    <td class="text-center p-1">
                        <input type="number" class="form-control py-1 text-center fs-12 balance_amount"
                            style="min-height: auto; width: 200px;" id="balance_amount{{ @$row->service_id }}"
                            name="balance_amount[{{ @$row->service_id }}]"
                            value="{{ @$row->service->service_charge - @$collection_amount }}" readonly>
                    </td>
                    <td class="text-center p-1">
                        <div class="custom-control custom-checkbox mx-auto">
                            <input type="checkbox" class="custom-control-input checkbox service_id"
                                id="check_{{ $row->service_id }}" name="service_id[]" value="{{ $row->service_id }}"
                                {{ @$collection_amount ? 'checked' : '' }}>
                            <label for="check_{{ $row->service_id }}" class="custom-control-label"></label>
                        </div>
                        <input type="hidden" name="client_id[{{ @$row->service_id }}]"
                            value="{{ @$row->client_id }}">
                    </td>
                </tr>
                @php
                    $total_amount += @$row->service->service_charge;
                    $total_collection += @$collection_amount;
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr class="bg-primary text-white">
                <th class="text-end p-1" colspan="3">Total</th>
                <th class="text-center p-1">
                    <input type="number" class="form-control py-1 text-center fs-12" style="min-height: auto;"
                        id="total_bill_amount" name="total_bill_amount" value="{{ $total_amount }}" readonly>
                </th>
                <th class="text-center p-1">
                    <input type="number" class="form-control py-1 text-center fs-12" style="min-height: auto;"
                        id="total_collection_amount" name="total_collection_amount" value="{{ $total_collection }}"
                        readonly>
                </th>
                <th class="text-center p-1">
                    <input type="number" class="form-control py-1 text-center fs-12" style="min-height: auto;"
                        id="total_balance_amount" name="total_balance_amount"
                        value="{{ $total_amount - $total_collection }}" readonly>
                </th>
                <th class="text-center p-1"></th>
            </tr>
        </tfoot>
    </table>
</div>
