<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\AccountTransactionAuto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AutomationApproveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $model = AccountTransactionAuto::with(['company', 'project'])
                ->whereNotIn('voucher_type', ['CV', 'DV', 'JV'])
                ->orderBY('id', 'desc')
                ->select('*', DB::raw('SUM(debit_amount) as amount'))
                ->orderBY('voucher_date', 'desc')->where('approve', 0)->groupBy('voucher_no');
            return DataTables::eloquent($model)
                ->addIndexColumn()
                ->addColumn('voucher_date', function ($row) {
                    return date('d-m-Y', strtotime($row->voucher_date));
                })
                ->addColumn('debit_head', function ($row) {
                    return AccountTransactionAuto::with('coa')->where('voucher_no', $row->voucher_no)
                        ->where('voucher_type', $row->voucher_type)
                        ->where('debit_amount', '>', 0)
                        ->get('coa_setup_id')->pluck('coa.head_name')->toArray();
                })
                ->addColumn('credit_head', function ($row) {
                    return AccountTransactionAuto::with('coa')->where('voucher_no', $row->voucher_no)
                        ->where('voucher_type', $row->voucher_type)
                        ->where('credit_amount', '>', 0)
                        ->get('coa_setup_id')->pluck('coa.head_name')->toArray();
                })
                ->addColumn('actions', function ($row) {
                    return '<div class="justify-content-end d-flex gap-1">
                            <a class="btn btn-outline-info btn-xs text-nowrap px-2 tt" href="' . Route('admin.automation-approve.show', $row->id) . '" data-bs-toggle="tooltip" data-bs-placement="top" title="View"><i class="fas fa-eye"></i> View</a>
                            <button type="button" class="btn btn-outline-danger btn-xs text-nowrap approve-btn px-2 tt" data-url="' . Route('admin.automation-approve.edit', $row->id) . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Approve"><i class="fa fa-thumbs-up"></i> Apporve</button></div>';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }
        $title = 'Approve Vouchers';
        $disable_filter = true;
        return view('admin.approve_automation.index', compact('title', 'disable_filter'));
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
        $title = "View Automation";
        $transaction = AccountTransactionAuto::findOrFail($id);
        $transactions = AccountTransactionAuto::where('voucher_no', $transaction->voucher_no)
            ->where('voucher_type', $transaction->voucher_type)->get();
        return view('admin.approve_automation.view')->with(compact('title', 'transaction', 'transactions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        if ($request->ajax()) {
            DB::transaction(function () use ($id) {
                $data = AccountTransactionAuto::findOrFail($id);
                AccountTransactionAuto::where('voucher_no', $data->voucher_no)
                    ->where('voucher_type', $data->voucher_type)
                    ->update([
                        'posted' => 1,
                        'approve' => 1,
                        'approve_by' => Auth::user()->id,
                    ]);
                $transactions = AccountTransactionAuto::where('voucher_no', $data->voucher_no)
                    ->where('voucher_type', $data->voucher_type)->get();
                foreach ($transactions as $item) {
                    AccountTransaction::create([
                        'company_id' => $item->company_id,
                        'account_transaction_auto_id' => $item->id,
                        'voucher_no' => $item->voucher_no,
                        'project_id' => $item->project_id,
                        'voucher_type' => $item->voucher_type,
                        'voucher_date' => $item->voucher_date,
                        'coa_setup_id' => $item->coa_setup_id,
                        'coa_head_code' => $item->coa_head_code,
                        'narration' => $item->narration,
                        'debit_amount' => $item->debit_amount,
                        'credit_amount' => $item->credit_amount,
                        'posted' => $item->posted,
                        'approve' => $item->approve,
                        'approve_by' => $item->approve_by,
                        'created_by' => $item->created_by,
                        'updated_by' => $item->updated_by
                    ]);
                }
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
