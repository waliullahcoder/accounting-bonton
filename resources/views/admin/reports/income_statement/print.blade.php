@extends('layouts.admin.print_app')
@section('content')
    <div class="mb-3">
        <table class="table table-bordered table-sm mb-0">
            <thead>
                <tr>
                    <th colspan="4" class="text-center">Income</th>
                </tr>
                <tr>
                    <th class="text-center" width="60">SL#</th>
                    <th>Head Code</th>
                    <th>Head Name</th>
                    <th class="text-right">Balance</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalIncome = 0;
                @endphp
                @foreach ($incomes as $incomeList)
                    @php
                        $totalIncome += abs($incomeList->debit_amount - $incomeList->credit_amount);
                    @endphp
                    <tr>
                        <th class="text-center">{{ $loop->iteration }}</th>
                        <td>{{ $incomeList->coa->head_code }}</td>
                        <td>{{ $incomeList->coa->head_name }}</td>
                        <td class="text-right">
                            {{ number_format(abs($incomeList->debit_amount - $incomeList->credit_amount), 2, '.', ',') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right">Total</th>
                    <th class="text-right">{{ number_format($totalIncome, 2, '.', ',') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mb-3">
        <table class="table table-bordered table-sm mb-0">
            <thead>
                <tr>
                    <th colspan="4" class="text-center">Expense</th>
                </tr>
                <tr>
                    <th class="text-center" width="60">SL#</th>
                    <th>Head Code</th>
                    <th>Head Name</th>
                    <th class="text-right">Balance</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalExpanse = 0;
                @endphp
                @foreach ($expenses as $expense)
                    @php
                        $totalExpanse += $expense->amount;
                    @endphp
                    <tr>
                        <th class="text-center">{{ $loop->iteration + 1 }}</th>
                        <td>{{ $expense->coa->head_code }}</td>
                        <td>{{ $expense->coa->head_name }}</td>
                        <td class="text-right">
                            {{ $expense->amount >= 0 ? number_format($expense->amount, 2, '.', ',') : '(' . number_format(abs($expense->amount), 2, '.', ',') . ')' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-right">Total</th>
                    <th class="text-right">
                        {{ $totalExpanse >= 0 ? number_format($totalExpanse, 2, '.', ',') : '(' . number_format(abs($totalExpanse), 2, '.', ',') . ')' }}
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mb-3">
        @if ($totalIncome > $totalExpanse)
            <h5 class="text-center mb-0" style="background-color: #00c292; color: #fff; padding: 0.5rem 0;">Net Profit:
                {{ number_format($totalIncome - $totalExpanse, 2, '.', ',') }}
            </h5>
        @endif

        @if ($totalIncome < $totalExpanse)
            <h5 class="text-center mb-0" style="background-color: #DC3545; color: #fff; padding: 0.5rem 0;">Net Lose:
                {{ number_format($totalExpanse - $totalIncome, 2, '.', ',') }}
            </h5>
        @endif
    </div>
    <div style="padding-top: 10px;">Print Date & Time : {{ date('d-m-Y h:i:s A') }}</div>
@endsection
