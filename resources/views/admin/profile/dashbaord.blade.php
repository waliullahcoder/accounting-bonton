@extends('layouts.admin.app')

@section('content')
<style>

.chart-legend-item,
.current-chart-legend {
    cursor: pointer;
    user-select: none;
    transition: all .2s ease;
}

.chart-legend-item:hover,
.current-chart-legend:hover {
    opacity: .7;
}

.chart-legend-item.legend-hidden,
.current-chart-legend.legend-hidden {
    opacity: .35;
    text-decoration: line-through;
}

</style>
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
    background: linear-gradient(135deg,
            #ffffff 0%,
            #f1fff8 100%);
}

.income-card::after {
    background: #10b981;
}

/* Expense */
.summary-card.expense-card {
    background: linear-gradient(135deg,
            #ffffff 0%,
            #fff5f5 100%);
}

.expense-card::after {
    background: #ef4444;
}

/* Balance */
.summary-card.balance-card {
    background: linear-gradient(135deg,
            #ffffff 0%,
            #f2f7ff 100%);
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
    background: linear-gradient(135deg,
            #d1fae5,
            #ecfdf5);
    color: #059669;
    box-shadow: 0 6px 15px rgba(16, 185, 129, .15);
}

.expense-icon {
    background: linear-gradient(135deg,
            #fee2e2,
            #fff1f2);
    color: #dc2626;
    box-shadow: 0 6px 15px rgba(239, 68, 68, .15);
}

.balance-icon {
    background: linear-gradient(135deg,
            #dbeafe,
            #eff6ff);
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

    .chart-header>div:last-child {
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


.chart-legend-item {
    cursor: pointer;
    user-select: none;
    transition: opacity 0.2s ease;
}

.chart-legend-item:hover {
    opacity: 0.7;
}

.chart-legend-item.legend-hidden {
    opacity: 0.4;
    text-decoration: line-through;
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
        <div class="card border-0 shadow-sm">

    {{-- Last 12 Months --}}
    <div class="card-header bg-white border-0 py-3">

        <div class="d-flex justify-content-between align-items-center">

            <div>
                <h5 class="mb-1 fw-bold">
                    Income & Expense
                </h5>

                <small class="text-muted">
                    Last 12 Months
                </small>
            </div>

            <div class="legend-box">

                <div class="legend-item chart-legend-item"
                     data-chart="monthly"
                     data-dataset="0"
                     id="incomeLegend">

                    <span class="legend-dot income-dot"></span>
                    Income

                </div>

                <div class="legend-item chart-legend-item"
                     data-chart="monthly"
                     data-dataset="1"
                     id="expenseLegend">

                    <span class="legend-dot expense-dot"></span>
                    Expense

                </div>

            </div>

        </div>

    </div>


    <div class="card-body">

        <div style="height: 350px;">
            <canvas id="monthlyIncomeExpenseChart"></canvas>
        </div>

    </div>


    {{-- Current Month --}}
<div class="card-header bg-white border-0 py-3 mt-3">

    <div class="d-flex justify-content-between align-items-center">

        <div>
            <h5 class="mb-1 fw-bold">
                Current Month
            </h5>

            <small class="text-muted">
                {{ now()->format('F Y') }}
            </small>
        </div>

    </div>

</div>


<div class="card-body">

    <div class="row">

        {{-- Chart --}}
        <div class="col-lg-7">

            <div style="height: 300px;">
                <canvas id="currentMonthIncomeExpenseChart"></canvas>
            </div>

        </div>


        {{-- Legend --}}
        <div class="col-lg-5">

            <div id="currentMonthChartLegend"
                 class="current-month-legend">
            </div>

        </div>

    </div>

</div>

    </div>


    <div class="card-body">

        <div style="height: 300px;">

            <canvas id="currentMonthIncomeExpenseChart"></canvas>

        </div>

    </div>

</div>

        @endsection


  @push('js')

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | DATA
    |--------------------------------------------------------------------------
    */

    const months = @json($months);

    const incomeData = @json($income);

    const expenseData = @json($expense);


    /*
    |--------------------------------------------------------------------------
    | CURRENT MONTH TOTAL DATA
    |--------------------------------------------------------------------------
    */

    const currentMonthIncome =
        Number(@json($currentMonthIncome ?? 0));

    const currentMonthExpense =
        Number(@json($currentMonthExpense ?? 0));


    /*
    |--------------------------------------------------------------------------
    | CURRENT MONTH HEAD WISE DATA
    |--------------------------------------------------------------------------
    */

    const currentMonthPieData =
        @json($currentMonthPieData ?? []);


    /*
    |--------------------------------------------------------------------------
    | MONTHLY BAR CHART
    |--------------------------------------------------------------------------
    */

    const monthlyCanvas =
        document.getElementById(
            'monthlyIncomeExpenseChart'
        );


    let monthlyChart = null;


    if (monthlyCanvas) {

        const ctx =
            monthlyCanvas.getContext('2d');


        monthlyChart = new Chart(ctx, {

            type: 'bar',

            data: {

                labels: months,

                datasets: [

                    {
                        label: 'Income',

                        data: incomeData,

                        backgroundColor:
                            'rgba(25, 135, 84, 0.75)',

                        borderColor:
                            '#198754',

                        borderWidth: 1,

                        borderRadius: 7,

                        borderSkipped: false,

                        barPercentage: 0.65,

                        categoryPercentage: 0.75
                    },


                    {
                        label: 'Expense',

                        data: expenseData,

                        backgroundColor:
                            'rgba(220, 53, 69, 0.75)',

                        borderColor:
                            '#dc3545',

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


                /*
                |--------------------------------------------------------------------------
                | BAR CLICK
                |--------------------------------------------------------------------------
                | Bar click করলে Income Statement যাবে
                |--------------------------------------------------------------------------
                */

                onClick: function (event, elements) {

                    if (!elements.length) {
                        return;
                    }


                    const monthIndex =
                        elements[0].index;


                    /*
                    |--------------------------------------------------------------------------
                    | Clicked Month
                    |--------------------------------------------------------------------------
                    */

                    const clickedDate = new Date();

                    clickedDate.setDate(1);

                    clickedDate.setMonth(
                        clickedDate.getMonth()
                        - (11 - monthIndex)
                    );


                    const year =
                        clickedDate.getFullYear();


                    const month =
                        String(
                            clickedDate.getMonth() + 1
                        ).padStart(2, '0');


                    /*
                    |--------------------------------------------------------------------------
                    | Start Date
                    |--------------------------------------------------------------------------
                    */

                    const startDate =
                        `01-${month}-${year}`;


                    /*
                    |--------------------------------------------------------------------------
                    | End Date
                    |--------------------------------------------------------------------------
                    */

                    const lastDay =
                        new Date(
                            year,
                            clickedDate.getMonth() + 1,
                            0
                        ).getDate();


                    const endDate =
                        `${String(lastDay).padStart(2, '0')}-${month}-${year}`;


                    /*
                    |--------------------------------------------------------------------------
                    | Income Statement
                    |--------------------------------------------------------------------------
                    */

                    const url =
                        `{{ route('admin.income-statement.index') }}` +
                        `?print=&filter=1&project_id=` +
                        `&date_range=${encodeURIComponent(
                            startDate + ' to ' + endDate
                        )}`;


                    window.location.href = url;

                },


                /*
                |--------------------------------------------------------------------------
                | BAR HOVER
                |--------------------------------------------------------------------------
                */

                onHover: function (event, elements) {

                    event.native.target.style.cursor =
                        elements.length
                            ? 'pointer'
                            : 'default';

                },


                interaction: {

                    mode: 'nearest',

                    intersect: true

                },


                /*
                |--------------------------------------------------------------------------
                | PLUGINS
                |--------------------------------------------------------------------------
                */

                plugins: {

                    legend: {
                        display: false
                    },


                    tooltip: {

                        backgroundColor: '#1f2937',

                        padding: 12,


                        callbacks: {

                            label: function (context) {

                                const value =
                                    context.raw || 0;


                                return context.dataset.label
                                    + ': '
                                    + Number(value)
                                        .toLocaleString(
                                            undefined,
                                            {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            }
                                        );

                            }

                        }

                    }

                },


                /*
                |--------------------------------------------------------------------------
                | SCALES
                |--------------------------------------------------------------------------
                */

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

                            color:
                                'rgba(0, 0, 0, 0.05)'

                        },

                        ticks: {

                            color: '#7b8190',

                            callback: function (value) {

                                return Number(value)
                                    .toLocaleString();

                            }

                        }

                    }

                }

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | CURRENT MONTH PIE CHART
    |--------------------------------------------------------------------------
    */

    const currentCanvas =
        document.getElementById(
            'currentMonthIncomeExpenseChart'
        );


    let currentMonthChart = null;


    if (currentCanvas) {

        const ctx =
            currentCanvas.getContext('2d');


        /*
        |--------------------------------------------------------------------------
        | HEAD WISE LABELS
        |--------------------------------------------------------------------------
        */

        const currentMonthLabels =
            currentMonthPieData.map(function (item) {

                return item.name;

            });


        /*
        |--------------------------------------------------------------------------
        | HEAD WISE VALUES
        |--------------------------------------------------------------------------
        */

        const currentMonthValues =
            currentMonthPieData.map(function (item) {

                return Number(item.amount || 0);

            });


        /*
        |--------------------------------------------------------------------------
        | HEAD TYPES
        |--------------------------------------------------------------------------
        */

        const currentMonthTypes =
            currentMonthPieData.map(function (item) {

                return item.type;

            });


        /*
        |--------------------------------------------------------------------------
        | COLORS
        |--------------------------------------------------------------------------
        */

        const incomeColors = [

            '#198754',
            '#20c997',
            '#0d6efd',
            '#6610f2',
            '#0dcaf0',
            '#6f42c1',
            '#146c43',
            '#087990',
            '#0a58ca',
            '#520dc2'

        ];


        const expenseColors = [

            '#dc3545',
            '#fd7e14',
            '#ffc107',
            '#d63384',
            '#6c757d',
            '#b02a37',
            '#bb2d3b',
            '#e35d6a',
            '#cc9a06',
            '#984c0c'

        ];


        /*
        |--------------------------------------------------------------------------
        | Generate Head Wise Colors
        |--------------------------------------------------------------------------
        */

        const currentMonthColors =
            currentMonthPieData.map(function (item, index) {

                if (item.type === 'Income') {

                    return incomeColors[
                        index % incomeColors.length
                    ];

                }


                return expenseColors[
                    index % expenseColors.length
                ];

            });


        /*
        |--------------------------------------------------------------------------
        | PIE CHART
        |--------------------------------------------------------------------------
        */

        currentMonthChart = new Chart(ctx, {

            type: 'pie',

            data: {

                labels: currentMonthLabels,

                datasets: [

                    {

                        data: currentMonthValues,

                        backgroundColor:
                            currentMonthColors,

                        borderColor:
                            '#ffffff',

                        borderWidth: 2,

                        hoverOffset: 8

                    }

                ]

            },


            options: {

                responsive: true,

                maintainAspectRatio: false,


                /*
                |--------------------------------------------------------------------------
                | PIE CLICK
                |--------------------------------------------------------------------------
                */

                onClick: function (event, elements) {

                    if (!elements.length) {
                        return;
                    }


                    const index =
                        elements[0].index;


                    const legendItem =
                        document.querySelector(
                            `.current-chart-legend[data-index="${index}"]`
                        );


                    if (!legendItem) {
                        return;
                    }


                    const dataset =
                        currentMonthChart
                            .data
                            .datasets[0];


                    const currentValue =
                        dataset.data[index];


                    /*
                    |--------------------------------------------------------------------------
                    | Toggle Slice
                    |--------------------------------------------------------------------------
                    */

                    if (
                        currentValue === null ||
                        typeof currentValue === 'undefined'
                    ) {

                        dataset.data[index] =
                            currentMonthValues[index];

                        legendItem.classList.remove(
                            'legend-hidden'
                        );

                    } else {

                        dataset.data[index] =
                            null;

                        legendItem.classList.add(
                            'legend-hidden'
                        );

                    }


                    currentMonthChart.update();

                },


                plugins: {

                    legend: {
                        display: false
                    },


                    tooltip: {

                        backgroundColor: '#1f2937',

                        padding: 12,


                        callbacks: {

                            label: function (context) {

                                const value =
                                    context.raw || 0;


                                const type =
                                    currentMonthTypes[
                                        context.dataIndex
                                    ] || '';


                                return type
                                    + ': '
                                    + Number(value)
                                        .toLocaleString(
                                            undefined,
                                            {
                                                minimumFractionDigits: 2,
                                                maximumFractionDigits: 2
                                            }
                                        );

                            }

                        }

                    }

                }

            }

        });


        /*
        |--------------------------------------------------------------------------
        | CURRENT MONTH DYNAMIC LEGEND
        |--------------------------------------------------------------------------
        */

        const legendContainer =
            document.getElementById(
                'currentMonthChartLegend'
            );


        if (legendContainer) {

            legendContainer.innerHTML = '';


            currentMonthPieData.forEach(
                function (item, index) {

                    const legendItem =
                        document.createElement('div');


                    legendItem.className =
                        'legend-item current-chart-legend';


                    legendItem.setAttribute(
                        'data-index',
                        index
                    );


                    legendItem.innerHTML = `

                        <span
                            class="legend-dot"
                            style="
                                background:${currentMonthColors[index]};
                            "
                        ></span>

                        <span class="legend-name">

                            ${item.name}

                        </span>

                        <span class="legend-type">

                            ${item.type}

                        </span>

                        <span class="legend-amount">

                            ${Number(item.amount || 0)
                                .toLocaleString(
                                    undefined,
                                    {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    }
                                )}

                        </span>

                    `;


                    /*
                    |--------------------------------------------------------------------------
                    | LEGEND CLICK
                    |--------------------------------------------------------------------------
                    */

                    legendItem.addEventListener(
                        'click',
                        function (e) {

                            e.preventDefault();

                            e.stopPropagation();


                            if (!currentMonthChart) {
                                return;
                            }


                            const index =
                                parseInt(
                                    this.getAttribute(
                                        'data-index'
                                    )
                                );


                            const dataset =
                                currentMonthChart
                                    .data
                                    .datasets[0];


                            const currentValue =
                                dataset.data[index];


                            /*
                            |--------------------------------------------------------------------------
                            | Hide
                            |--------------------------------------------------------------------------
                            */

                            if (
                                currentValue === null ||
                                typeof currentValue === 'undefined'
                            ) {

                                dataset.data[index] =
                                    currentMonthValues[index];


                                this.classList.remove(
                                    'legend-hidden'
                                );

                            }


                            /*
                            |--------------------------------------------------------------------------
                            | Show
                            |--------------------------------------------------------------------------
                            */

                            else {

                                dataset.data[index] =
                                    null;


                                this.classList.add(
                                    'legend-hidden'
                                );

                            }


                            currentMonthChart.update();

                        }
                    );


                    legendContainer.appendChild(
                        legendItem
                    );

                }
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | LAST 12 MONTHS LEGEND CLICK
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.chart-legend-item')
        .forEach(function (item) {

            item.addEventListener('click', function (e) {

                e.preventDefault();

                e.stopPropagation();


                if (!monthlyChart) {
                    return;
                }


                const datasetIndex =
                    parseInt(
                        this.getAttribute(
                            'data-dataset'
                        )
                    );


                const visible =
                    monthlyChart.isDatasetVisible(
                        datasetIndex
                    );


                /*
                |--------------------------------------------------------------------------
                | Toggle
                |--------------------------------------------------------------------------
                */

                monthlyChart.setDatasetVisibility(
                    datasetIndex,
                    !visible
                );


                monthlyChart.update();


                /*
                |--------------------------------------------------------------------------
                | Legend Style
                |--------------------------------------------------------------------------
                */

                if (visible) {

                    this.classList.add(
                        'legend-hidden'
                    );

                } else {

                    this.classList.remove(
                        'legend-hidden'
                    );

                }

            });

        });


    /*
    |--------------------------------------------------------------------------
    | CURRENT MONTH PIE LEGEND CLICK
    |--------------------------------------------------------------------------
    |
    | এই অংশটি dynamic legend-এর জন্য।
    | তাই এখানে আর data-dataset ব্যবহার হবে না।
    |
    */

    document
        .querySelectorAll('.current-chart-legend')
        .forEach(function (item) {

            /*
            |--------------------------------------------------------------------------
            | Important
            |--------------------------------------------------------------------------
            | Dynamic legend উপরে তৈরি হওয়ার কারণে
            | এখানে listener পুনরায় না দিলেও কাজ করবে।
            |
            */

        });

});

</script>

@endpush