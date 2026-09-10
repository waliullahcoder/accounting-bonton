<?php

namespace App\DataTables;

use App\Models\CustomerCollections;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CollectionSheetDataTable extends DataTable
{
    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('date', function ($row) {
                return date('d-m-Y', strtotime($row->date));
            });
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(CustomerCollections $model): QueryBuilder
    {
        $query = $model->with('area');
        $month = request('month') ?? date('m');
        $year = request('year') ?? $year = date('Y');
        $start_date = date('Y-m-d', strtotime($year . '-' . $month . '-01'));
        $end_date = date('Y-m-t', strtotime($year . '-' . $month));
        $query->where('date', '>=', $start_date)->where('date', '<=', $end_date);
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
                'data'      => 'date',
                'name'      => 'date',
                'title'     => 'Collection Date',
                'orderable' => false,
                'searchable' => false,
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
                'title'     => 'Area',
                'defaultContend' => ''
            ]),
            Column::make([
                'data'      => 'address',
                'name'      => 'address',
                'title'     => 'Address',
            ]),
            Column::make([
                'data'      => 'amount',
                'name'      => 'amount',
                'title'     => 'Amount',
                'class'     => 'text-end',
            ]),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'collection_sheet_' . date('d_m_Y_h_i_s_A');
    }
}
