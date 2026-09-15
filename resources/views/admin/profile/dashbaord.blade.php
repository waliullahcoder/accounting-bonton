@extends('layouts.admin.app')

@section('content')
<style>
    .dashboard-wrapper {
        padding: 12px 0 25px;
        background: #f6f8fc;
    }

    /* =========================
       SUMMARY CARDS
    ========================== */

    .dashboard-card {
        position: relative;
        border: 1px solid rgba(226, 232, 240, .8);
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 8px 30px rgba(15, 23, 42, .06);
        overflow: hidden;
        transition: all .3s ease;
    }

    .dashboard-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 35px rgba(15, 23, 42, .10);
    }

   .summary-card {
    min-height: 145px;
    padding: 24px;

    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    text-align: center;
}

    .summary-card::after {
        content: "";
        position: absolute;
        right: -35px;
        bottom: -45px;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        opacity: .07;
    }

    /* Income */
    .summary-card.income-card {
        background: linear-gradient(
            135deg,
            #ffffff 0%,
            #f1fff8 100%
        );
    }

    .income-card::after {
        background: #10b981;
    }

    /* Expense */
    .summary-card.expense-card {
        background: linear-gradient(
            135deg,
            #ffffff 0%,
            #fff5f5 100%
        );
    }

    .expense-card::after {
        background: #ef4444;
    }

    /* Balance */
    .summary-card.balance-card {
        background: linear-gradient(
            135deg,
            #ffffff 0%,
            #f2f7ff 100%
        );
    }

    .balance-card::after {
        background: #3b82f6;
    }

    .summary-icon {
    width: 54px;
    height: 54px;
    border-radius: 15px;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 21px;
    margin-bottom: 12px;
}

    .income-icon {
        background: linear-gradient(
            135deg,
            #d1fae5,
            #ecfdf5
        );
        color: #059669;
        box-shadow: 0 6px 15px rgba(16, 185, 129, .15);
    }

    .expense-icon {
        background: linear-gradient(
            135deg,
            #fee2e2,
            #fff1f2
        );
        color: #dc2626;
        box-shadow: 0 6px 15px rgba(239, 68, 68, .15);
    }

    .balance-icon {
        background: linear-gradient(
            135deg,
            #dbeafe,
            #eff6ff
        );
        color: #2563eb;
        box-shadow: 0 6px 15px rgba(59, 130, 246, .15);
    }

   .summary-title {
    font-size: 13px;
    color: #64748b;
    margin-bottom: 5px;
    font-weight: 600;
    text-align: center;
}

   .summary-value {
    font-size: 27px;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -.5px;
    text-align: center;
}

    /* =========================
       CHART CARD
    ========================== */

    .chart-card {
        border: 1px solid rgba(226, 232, 240, .85);
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 8px 30px rgba(15, 23, 42, .06);
        overflow: hidden;
    }

    .chart-header {
        padding: 24px 26px 15px;

        display: flex;
        align-items: center;
        justify-content: space-between;

        flex-wrap: wrap;
        gap: 15px;

        border-bottom: 1px solid #f1f5f9;
    }

    .chart-title {
        margin: 0;

        font-size: 20px;
        font-weight: 750;

        color: #0f172a;
        letter-spacing: -.3px;
    }

    .chart-title i {
        color: #2563eb;
    }

    .chart-subtitle {
        margin-top: 6px;

        color: #94a3b8;
        font-size: 13px;
        font-weight: 500;
    }

    /* =========================
       LEGEND
    ========================== */

    .legend-box {
        display: flex;
        gap: 18px;
        align-items: center;

        font-size: 13px;
        color: #64748b;
        font-weight: 500;
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
        display: inline-block;
    }

    .income-dot {
        background: #10b981;
        box-shadow: 0 0 0 4px rgba(16, 185, 129, .10);
    }

    .expense-dot {
        background: #ef4444;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, .10);
    }

    /* =========================
       YEAR SELECT
    ========================== */

    .year-select {
        min-width: 100px;

        border: 1px solid #e2e8f0;
        border-radius: 10px;

        padding: 9px 34px 9px 13px;

        background: #f8fafc;

        color: #334155;

        font-size: 13px;
        font-weight: 600;

        outline: none;

        cursor: pointer;

        transition: all .2s ease;
    }

    .year-select:hover {
        border-color: #93c5fd;
        background: #ffffff;
    }

    .year-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, .10);
    }

    /* =========================
       CHART
    ========================== */

    .chart-body {
        padding: 18px 25px 28px;
        height: 440px;
        position: relative;
    }

    /* =========================
       RESPONSIVE
    ========================== */

    @media (max-width: 992px) {

        .chart-header {
            align-items: flex-start;
        }

        .chart-body {
            height: 400px;
        }
    }

    @media (max-width: 768px) {

        .dashboard-wrapper {
            padding-left: 5px;
            padding-right: 5px;
        }

        .summary-card {
            min-height: 135px;
            padding: 20px;
        }

        .summary-value {
            font-size: 23px;
        }

        .chart-header {
            padding: 20px 18px 14px;
        }

        .chart-body {
            height: 350px;
            padding: 12px 10px 20px;
        }

        .legend-box {
            gap: 12px;
        }
    }

    @media (max-width: 576px) {

        .chart-header {
            display: block;
        }

        .chart-header > div:last-child {
            margin-top: 15px;
            justify-content: space-between;
        }

        .legend-box {
            margin-bottom: 12px;
        }

        .chart-title {
            font-size: 18px;
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
    <div class="dashboard-card summary-card income-card">

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
    <div class="dashboard-card summary-card expense-card">

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
    <div class="dashboard-card summary-card balance-card">

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