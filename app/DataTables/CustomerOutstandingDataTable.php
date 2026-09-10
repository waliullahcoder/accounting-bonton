<?php

namespace App\DataTables;

use App\Models\Customer;
use App\Models\CustomerDeliveries;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CustomerOutstandingDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $month = request('month') ?? date('m');
        $year = request('year') ?? $year = date('Y');

        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('prev_outstanding', function ($row) use ($year, $month) {
                $start_date = date('Y-m-d', strtotime($year . '-' . $month . '-01'));
                return CustomerDeliveries::where('date', '<', $start_date)->where('customer_id', $row->id)->where('collected', 0)->sum('subtotal');
            })
            ->addColumn('curr_outstanding', function ($row) use ($year, $month) {
                $start_date = date('Y-m-d', strtotime($year . '-' . $month . '-01'));
                $end_date = date('Y-m-t', strtotime($year . '-' . $month));
                return CustomerDeliveries::where('date', '>=', $start_date)->where('date', '<=', $end_date)->where('customer_id', $row->id)->where('collected', 0)->sum('subtotal');
            })
            ->addColumn('total_outstanding', function ($row) use ($year, $month) {
                $end_date = date('Y-m-t', strtotime($year . '-' . $month));
                return CustomerDeliveries::where('date', '<', $end_date)->where('customer_id', $row->id)->where('collected', 0)->sum('subtotal');
            });
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Customer $model): QueryBuilder
    {
        $query = $model->with('area')->whereHas('viewDeliveries', function ($q) {
            $month = request('month') ?? date('m');
            $year = request('year') ?? $year = date('Y');
            $end_date = date('Y-m-t', strtotime($year . '-' . $month));
            $q->where('collected', 0)->where('date', '<=', $end_date);
        });
        if (request('customer_id')) {
            $query->whereIn('id', request('customer_id'));
        }
        return $query->where('status', 1)->orderBy('name', 'asc');
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('dataTable')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom('Bfrtip')
            ->selectStyleSingle()
            ->parameters([
                'buttons'      => [
                    Button::make('reload'),
                    [
                        'extend'  => 'excel',
                        'text'    => '<i class="fal fa-file-spreadsheet"></i> Exel',
                    ],
                    [
                        'text'    => '<i class="fal fa-file-pdf"></i> Print',
                        'className' => 'getPdf',
                    ],
                ],
                'responsive' => true,
                'pageLength' => 20,
                'drawCallback' => 'function() {
                    let data = this.api().ajax.json().data;
                    var total_prev = 0;
                    var total_curr = 0;
                    var total_outstanding = 0;
                    data.forEach(function(item, index){
                        total_prev += +item.prev_outstanding;
                        total_curr += +item.curr_outstanding;
                        total_outstanding += +item.total_outstanding;
                    });
                    $("#total_prev").html(total_prev);
                    $("#total_curr").html(total_curr);
                    $("#total_outstanding").html(total_outstanding);
                }'
            ]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::make([
                'data'      => "DT_RowIndex",
                'name'      => "DT_RowIndex",
                'title'     => 'SL.',
                'orderable' => false,
                'searchable' => false,
                'width'     => '30',
                'class'     => 'text-center',
            ]),
            Column::make([
                'data'      => 'name',
                'name'      => 'name',
                'title'     => 'Customer Name',
            ]),
            Column::make([
                'data'      => 'phone',
                'name'      => 'phone',
                'title'     => 'Customer Phone',
            ]),
            Column::make([
                'data'      => 'area.name',
                'name'      => 'area.name',
                'title'     => 'Area Name',
                'defaultContent' => '',
            ]),
            Column::make([
                'data'      => 'address',
                'name'      => 'address',
                'title'     => 'Address',
                'footer'    => '<div class="text-end">Total Summary</div>',
            ]),
            Column::make([
                'data'      => 'prev_outstanding',
                'name'      => 'prev_outstanding',
                'title'     => 'Previous outstanding',
                'orderable' => false,
                'searchable' => false,
                'class'     => 'text-end',
                'footer'    => '<span id="total_prev"></span>',
            ]),
            Column::make([
                'data'      => 'curr_outstanding',
                'name'      => 'curr_outstanding',
                'title'     => 'Current outstanding',
                'orderable' => false,
                'searchable' => false,
                'class'     => 'text-end',
                'footer'    => '<span id="total_curr"></span>',
            ]),
            Column::make([
                'data'      => 'total_outstanding',
                'name'      => 'total_outstanding',
                'title'     => 'Total outstanding',
                'orderable' => false,
                'searchable' => false,
                'class'     => 'text-end',
                'footer'    => '<span id="total_outstanding"></span>',
            ]),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'customer_outstanding_' . date('d_m_Y_h_i_s_A');
    }
}
