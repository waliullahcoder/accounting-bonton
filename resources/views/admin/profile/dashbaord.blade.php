@extends('layouts.admin.app')

@section('content')

<style>
    .dashboard-wrapper {
        padding: 10px 0;
    }

    .dashboard-card {
        border: 0;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.06);
        transition: all .3s ease;
        overflow: hidden;
    }

    .dashboard-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.09);
    }

    .summary-card {
        position: relative;
        padding: 22px;
        min-height: 135px;
    }

    .summary-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: 12px;
    }

    .income-icon {
        background: rgba(25, 135, 84, .10);
        color: #198754;
    }

    .expense-icon {
        background: rgba(220, 53, 69, .10);
        color: #dc3545;
    }

    .balance-icon {
        background: rgba(13, 110, 253, .10);
        color: #0d6efd;
    }

    .summary-title {
        font-size: 14px;
        color: #7b8190;
        margin-bottom: 5px;
        font-weight: 500;
    }

    .summary-value {
        font-size: 25px;
        font-weight: 700;
        color: #1f2937;
    }

    .chart-card {
        border: 0;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 5px 25px rgba(0, 0, 0, .06);
    }

    .chart-header {
        padding: 22px 24px 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }

    .chart-title {
        margin: 0;
        font-size: 19px;
        font-weight: 700;
        color: #1f2937;
    }

    .chart-subtitle {
        margin-top: 5px;
        color: #8a91a0;
        font-size: 13px;
    }

    .year-select {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 8px 35px 8px 12px;
        font-size: 14px;
        color: #374151;
        outline: none;
    }

    .chart-body {
        padding: 10px 24px 25px;
        height: 430px;
    }

    .legend-box {
        display: flex;
        gap: 20px;
        align-items: center;
        font-size: 13px;
        color: #6b7280;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }

    .income-dot {
        background: #198754;
    }

    .expense-dot {
        background: #dc3545;
    }

    @media(max-width: 768px) {
        .chart-body {
            height: 350px;
            padding: 5px 10px 20px;
        }

        .chart-header {
            padding: 18px 15px 10px;
        }

        .summary-value {
            font-size: 21px;
        }
    }
</style>


<div class="container-fluid dashboard-wrapper">

    {{-- =========================
        SUMMARY CARDS
    ========================== --}}
    <div class="row g-4 mb-4">

        {{-- Total Income --}}
        <div class="col-xl-4 col-md-6">
            <div class="dashboard-card summary-card">

                <div class="summary-icon income-icon">
                    <i class="fas fa-arrow-down"></i>
                </div>

                <div class="summary-title">
                    Total Income
                </div>

                <div class="summary-value">
                    {{ number_format($totalIncome, 2) }}
                </div>

            </div>
        </div>


        {{-- Total Expense --}}
        <div class="col-xl-4 col-md-6">
            <div class="dashboard-card summary-card">

                <div class="summary-icon expense-icon">
                    <i class="fas fa-arrow-up"></i>
                </div>

                <div class="summary-title">
                    Total Expense
                </div>

                <div class="summary-value">
                    {{ number_format($totalExpense, 2) }}
                </div>

            </div>
        </div>


        {{-- Net Balance --}}
        <div class="col-xl-4 col-md-12">
            <div class="dashboard-card summary-card">

                <div class="summary-icon balance-icon">
                    <i class="fas fa-wallet"></i>
                </div>

                <div class="summary-title">
                    Net Balance
                </div>

                <div class="summary-value">
                    {{ number_format($netBalance, 2) }}
                </div>

            </div>
        </div>

    </div>


    {{-- =========================
        MONTHLY INCOME & EXPENSE
    ========================== --}}
    <div class="chart-card">

        <div class="chart-header">

            <div>
                <h4 class="chart-title">
                    <i class="fas fa-chart-column me-2"></i>
                    Monthly Income & Expense
                </h4>

                <div class="chart-subtitle">
                    Financial overview for {{ $year }}
                </div>
            </div>


            <div class="d-flex align-items-center gap-3">

                <div class="legend-box">

                    <div class="legend-item">
                        <span class="legend-dot income-dot"></span>
                        Income
                    </div>

                    <div class="legend-item">
                        <span class="legend-dot expense-dot"></span>
                        Expense
                    </div>

                </div>


                <form method="GET" action="{{ route('admin.dashboard') }}">

                    <select
                        name="year"
                        class="year-select"
                        onchange="this.form.submit()"
                    >

                        @for($i = now()->year - 4; $i <= now()->year; $i++)

                            <option
                                value="{{ $i }}"
                                {{ $year == $i ? 'selected' : '' }}
                            >
                                {{ $i }}
                            </option>

                        @endfor

                    </select>

                </form>

            </div>

        </div>


        <div class="chart-body">
            <canvas id="monthlyIncomeExpenseChart"></canvas>
        </div>

    </div>

</div>

@endsection


@push('js')

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    const ctx = document
        .getElementById('monthlyIncomeExpenseChart')
        .getContext('2d');


    const months = @json($months);

    const incomeData = @json($income);

    const expenseData = @json($expense);


    new Chart(ctx, {

        type: 'bar',

        data: {

            labels: months,

            datasets: [

                {
                    label: 'Income',

                    data: incomeData,

                    backgroundColor: 'rgba(25, 135, 84, 0.75)',

                    borderColor: '#198754',

                    borderWidth: 1,

                    borderRadius: 7,

                    borderSkipped: false,

                    barPercentage: 0.65,

                    categoryPercentage: 0.75
                },

                {
                    label: 'Expense',

                    data: expenseData,

                    backgroundColor: 'rgba(220, 53, 69, 0.75)',

                    borderColor: '#dc3545',

                    borderWidth: 1,

                    borderRadius: 7,

                    borderSkipped: false,

                    barPercentage: 0.65,

                    categoryPercentage: 0.75
                }

            ]

        },


        options: {

            responsive: true,

            maintainAspectRatio: false,

            interaction: {
                mode: 'index',
                intersect: false
            },

            plugins: {

                legend: {
                    display: false
                },

                tooltip: {

                    backgroundColor: '#1f2937',

                    padding: 12,

                    titleFont: {
                        size: 14,
                        weight: '600'
                    },

                    bodyFont: {
                        size: 13
                    },

                    callbacks: {

                        label: function(context) {

                            let value = context.raw || 0;

                            return context.dataset.label
                                + ': '
                                + value.toLocaleString(undefined, {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                        }

                    }

                }

            },


            scales: {

                x: {

                    grid: {
                        display: false
                    },

                    ticks: {
                        color: '#7b8190',
                        font: {
                            size: 12
                        }
                    }

                },


                y: {

                    beginAtZero: true,

                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },

                    ticks: {

                        color: '#7b8190',

                        callback: function(value) {

                            return Number(value).toLocaleString();

                        }

                    }

                }

            }

        }

    });

});

</script>

@endpush