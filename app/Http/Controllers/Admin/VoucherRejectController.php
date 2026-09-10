<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\AccountTransactionAuto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class VoucherRejectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $model = AccountTransaction::with(['company', 'project'])
                ->whereIn('voucher_type', ['CV', 'DV', 'JV'])
                ->select('*', DB::raw('SUM(debit_amount) as amount'))
                ->orderBY('id', 'desc')
                ->orderBY('voucher_date', 'desc')->where('approve', 1)->groupBy('voucher_no');
            return DataTables::eloquent($model)
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
                ->addColumn('actions', function ($row) {
                    return '<div class="justify-content-end d-flex gap-1">
                            <a class="btn btn-outline-info btn-xs text-nowrap px-2 tt" href="' . Route('admin.voucher-reject.show', $row->id) . '" data-bs-toggle="tooltip" data-bs-placement="top" title="View"><i class="fas fa-eye"></i> View</a>
                            <button type="button" class="btn btn-outline-danger btn-xs text-nowrap approve-btn px-2 tt" data-url="' . Route('admin.voucher-reject.edit', $row->id) . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Approve"><i class="fa fa-thumbs-down"></i> Refuse</button></div>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        $title = 'Refuse Vouchers';
        $disable_filter = true;
        return view('admin.refuse_voucher.index', compact('title', 'disable_filter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $title = "View Voucher";
        $transaction = AccountTransaction::findOrFail($id);
        $transactions = AccountTransaction::where('voucher_no', $transaction->voucher_no)
            ->where('voucher_type', $transaction->voucher_type)->get();
        return view('admin.refuse_voucher.view')->with(compact('title', 'transaction', 'transactions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        if ($request->ajax()) {
            DB::transaction(function () use ($id) {
                $data = AccountTransaction::findOrFail($id);
                $transactions = AccountTransaction::where('voucher_no', $data->voucher_no)
                    ->where('voucher_type', $data->voucher_type)->get();
                foreach ($transactions as $item) {
                    $query = AccountTransactionAuto::find($item->account_transaction_auto_id);
                    $query->update([
                        'posted' => 0,
                        'approve' => 0,
                        'approve_by' => NULL,
                    ]);
                }
                AccountTransaction::where('voucher_no', $data->voucher_no)
                    ->where('voucher_type', $data->voucher_type)
                    ->forceDelete();
            });

            return response()->json(['status' => 'success']);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
