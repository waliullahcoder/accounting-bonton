@extends('layouts.admin.app')

@section('content')
    <div class="card">
        <div class="card-header pe-2 py-2">
            <div class="d-flex flex-wrap justify-content-between gap-2 align-items-center">
                <h6 class="h6 mb-0 text-uppercase text-nowrap flex-grow-1">
                    {{ @$title ?? 'Please Set Title' }}</h6>
                <a href="{{ Route('admin.client-collection.index') }}" class="btn btn-primary btn-sm">Go Back</a>
            </div>
        </div>
        <div class="card-body px-3">
            <div class="table-responsive-sm">
                <table class="table table-borderless table-striped mb-0">
                    <tbody>
                        <tr>
                            <th width="200">Serial No.</th>
                            <th width="10">:</th>
                            <td>{{ @$data->serial_no }}</td>
                        </tr>
                        <tr>
                            <th width="200">Date</th>
                            <th width="10">:</th>
                            <td>{{ date('d-m-Y', strtotime(@$data->date)) }}</td>
                        </tr>
                        <tr>
                            <th width="200">Month</th>
                            <th width="10">:</th>
                            <td>{{ @$data->month }}</td>
                        </tr>
                        <tr>
                            <th width="200">Year</th>
                            <th width="10">:</th>
                            <td>{{ @$data->year }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="table-responsive-sm mt-4">
                <table class="table mb-0">
                    <thead>
                        <tr class="bg-primary text-white text-nowrap text-uppercase">
                            <th class="p-1 text-center" width="50">SL#</th>
                            <th class="p-1">Client Name</th>
                            <th class="p-1">Software</th>
                            <th class="p-1 text-center" width="200">Bill</th>
                            <th class="p-1 text-center" width="200">Collection</th>
                            <th class="p-1 text-center" width="200">Balance</th>
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
                                <td class="p-1">{{ @$row->client->name }}</td>
                                <td class="p-1">{{ @$row->service->software_type }}</td>
                                <td class="text-center p-1">
                                    <input type="number" class="form-control py-1 text-center fs-12"
                                        style="min-height: auto; width: 200px;" id="bill_amount{{ @$row->service_id }}"
                                        name="bill_amount[{{ @$row->service_id }}]"
                                        value="{{ @$row->service->service_charge }}" readonly>
                                </td>
                                <td class="text-center p-1">
                                    <input type="number" class="form-control py-1 text-center fs-12 collection_amount"
                                        style="min-height: auto; width: 200px;" data-id="{{ @$row->service_id }}"
                                        id="collection_amount{{ @$row->service_id }}"
                                        max="{{ @$row->service->service_charge }}"
                                        name="collection_amount[{{ @$row->service_id }}]"
                                        value="{{ @$collection_amount }}">
                                </td>
                                <td class="text-center p-1">
                                    <input type="number" class="form-control py-1 text-center fs-12 balance_amount"
                                        style="min-height: auto; width: 200px;" id="balance_amount{{ @$row->service_id }}"
                                        name="balance_amount[{{ @$row->service_id }}]"
                                        value="{{ @$row->service->service_charge - @$collection_amount }}" readonly>
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
                                    id="total_collection_amount" name="total_collection_amount"
                                    value="{{ $total_collection }}" readonly>
                            </th>
                            <th class="text-center p-1">
                                <input type="number" class="form-control py-1 text-center fs-12" style="min-height: auto;"
                                    id="total_balance_amount" name="total_balance_amount"
                                    value="{{ $total_amount - $total_collection }}" readonly>
                            </th>
                        </tr>
                    </tfoot>
                </table>

            </div>
        </div>
    </div>
@endsection
