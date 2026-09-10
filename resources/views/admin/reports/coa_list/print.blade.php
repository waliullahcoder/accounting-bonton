@extends('layouts.admin.print_app')
@section('content')
    <table class="table table-bordered table-condensed table-striped align-middle">
        <thead class="text-nowrap">
            <tr>
                <th class="text-center" width="30px">SL#</th>
                <th>Head Code</th>
                <th>Head Name</th>
                <th>Head Type</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    <td class="text-center" width="30px">{{ $loop->iteration }}</td>
                    <td>{{ $row->head_code }}</td>
                    <td>{{ $row->head_name }}</td>
                    <td>{{ ($row->head_type == 'A' ? 'Asset' : '') . ($row->head_type == 'L' ? 'Liabilities' : '') . ($row->head_type == 'I' ? 'Income' : '') . ($row->head_type == 'E' ? 'Expense' : '') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div style="padding-top: 10px;">Print Date & Time : {{ date('d-m-Y h:i:s A') }}</div>
@endsection
