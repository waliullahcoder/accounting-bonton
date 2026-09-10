<?php

namespace App\DataTables;

use App\Models\AccountTransaction;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class voucherListDataTable extends DataTable
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
            ->addColumn('voucher_date', function ($row) {
                return date('d-m-Y', strtotime($row->voucher_date));
            })
            ->addColumn('debit_head', function ($row) {
                return AccountTransaction::with('coa')->where('voucher_no', $row->voucher_no)
                    ->where('voucher_type', $row->voucher_type)
                    ->where('debit_amount', '>', 0)
                    ->get('coa_setup_id')->pluck('coa.head_name')->toArray();
            })
            ->addColumn('credit_head', function ($row) {
                return AccountTransaction::with('coa')->where('voucher_no', $row->voucher_no)
                    ->where('voucher_type', $row->voucher_type)
                    ->where('credit_amount', '>', 0)
                    ->get('coa_setup_id')->pluck('coa.head_name')->toArray();
            })
            ->addColumn('total_amount', function ($row) {
                $query = AccountTransaction::query();
                $voucher_type = request('voucher_type');
                $project_id = request('project_id');
                $date_range = explode('to', request('date_range'));
                $start_date = !is_null(request('date_range')) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
                $end_date = !is_null(request('date_range')) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');
                if (!is_null($voucher_type)) {
                    $query->where('voucher_type', $voucher_type);
                }
                if (!is_null($project_id)) {
                    $query->where('project_id', $project_id);
                }
                $query->where('voucher_date', '>=', $start_date)->where('voucher_date', '<=', $end_date);
                $total = $query->select('*', DB::raw('SUM(debit_amount) as amount'))->orderBY('id', 'desc')
                    ->orderBY('voucher_date', 'desc')
                    ->groupBy('voucher_no')->get();
                return number_format($total->sum('amount'), 2, '.', '');
            });
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Request $request, AccountTransaction $model): QueryBuilder
    {
        $query = $model->with('project');
        $voucher_type = $request->voucher_type;
        $project_id = $request->project_id;
        $date_range = explode('to', $request->date_range);
        $start_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
        $end_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');
        if (!is_null($voucher_type)) {
            $query->where('voucher_type', $voucher_type);
        }
        if (!is_null($project_id)) {
            $query->where('project_id', $project_id);
        }
        $query->where('voucher_date', '>=', $start_date)->where('voucher_date', '<=', $end_date);
        return $query->select('*', DB::raw('SUM(debit_amount) as amount'))->orderBY('id', 'desc')
            ->orderBY('voucher_date', 'desc')
            ->groupBy('voucher_no');
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
                'drawCallback' => 'function() {
                    let data = this.api().ajax.json().data;
                    var total_amount = 0;
                    data.forEach(function(item, index){
                        total_amount = item.total_amount;
                    });
                    $("#total_amount").html(total_amount);
                 }',
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
                'title'     => 'SL#',
                'orderable' => false,
                'searchable' => false,
                'width'     => '60',
                'class'     => 'text-center',
            ]),
            Column::make([
                'data'      => "voucher_date",
                'name'      => "voucher_date",
                'title'     => 'Date',
            ]),
            Column::make([
                'data'      => "project.name",
                'name'      => "project.name",
                'title'     => 'Project Name',
            ]),
            Column::make([
                'data'      => "voucher_type",
                'name'      => "voucher_type",
                'title'     => 'Voucher Type',
            ]),
            Column::make([
                'data'      => "voucher_no",
                'name'      => "voucher_no",
                'title'     => 'Voucher No',
            ]),
            Column::make([
                'data'      => "debit_head",
                'name'      => "debit_head",
                'title'     => 'Debit Head',
            ]),
            Column::make([
                'data'      => "credit_head",
                'name'      => "credit_head",
                'title'     => 'Credit Head',
            ]),
            Column::make([
                'data'      => "amount",
                'name'      => "amount",
                'title'     => 'Amount',
                'footer'    => '<span id="total_amount"></span>',
            ]),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'voucher_list_' . date('d_m_Y_h_i_s_A');
    }
}
