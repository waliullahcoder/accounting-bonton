<?php

namespace App\Http\Controllers\Admin;

use App\Models\CoaSetup;
use App\Models\TrialBalance;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\AccountTransaction;
use App\DataTables\CoaListDataTable;
use App\Http\Controllers\Controller;
use App\DataTables\voucherListDataTable;

class ReportController extends Controller
{
    public function coaList(Request $request, CoaListDataTable $dataTable)
    {
        if ($request->ajax() && $request->has('getHeads')) {
            $value = $request->head_type;
            if ($value == 'gl') {
                $heads = CoaSetup::where('general', 1)->orderBy('head_name', 'asc')->get();
            } else {
                $heads = CoaSetup::whereNull('parent_id')->orderBy('head_name', 'asc')->get();
            }
            return response()->json(['status' => 'success', 'heads' => $heads]);
        }

        if (!is_null($request->print)) {
            $query = CoaSetup::query();
            $parent_head = $request->parent_head;
            if ($parent_head) {
                $query->where('head_code', 'LIKE', $parent_head . '%')->where('transaction', 1);
            }
            $data = $query->orderBy('head_name', 'asc')->get();

            $report_title = 'Chart of Accounts';
            $pdf = Pdf::loadView('admin.reports.coa_list.print', compact('report_title', 'data'));
            $pdf->setPaper('A4');
            return $pdf->stream('coa_list_' . date('d_m_Y_h_i_s') . '.pdf');
        }

        $title = 'Chart of Accounts';
        return $dataTable->render('admin.reports.coa_list.index', compact('title'));
    }

    public function voucherList(Request $request, voucherListDataTable $dataTable)
    {
        if ($request->ajax() && $request->has('getHeads')) {
            $value = $request->head_type;
            if ($value == 'gl') {
                $heads = CoaSetup::where('general', 1)->orderBy('head_name', 'asc')->get();
            } else {
                $heads = CoaSetup::whereNull('parent_id')->orderBy('head_name', 'asc')->get();
            }
            return response()->json(['status' => 'success', 'heads' => $heads]);
        }

        if (!is_null($request->print)) {
            $query = AccountTransaction::query();
            $voucher_type = $request->voucher_type;
            $date_range = explode('to', $request->date_range);
            $start_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
            $end_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');
            if (!is_null($request->project_id)) {
                $query->where('project_id', $request->project_id);
            }
            if (!is_null($voucher_type)) {
                $query->where('voucher_type', $voucher_type);
            }
            if (!is_null($start_date) && !is_null($end_date)) {
                $query->where('voucher_date', '>=', $start_date)->where('voucher_date', '<=', $end_date);
            }

            $data = $query->select('*', DB::raw('SUM(debit_amount) as amount'))->orderBY('id', 'desc')
                ->orderBY('voucher_date', 'desc')
                ->groupBy('voucher_no')->get();

            $report_title = 'Voucher List';
            $pdf = Pdf::loadView('admin.reports.voucher_list.print', compact('report_title', 'data'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('voucher_list_' . date('d_m_Y_h_i_s') . '.pdf');
        }

        $title = 'Voucher List';
        $voucher_types = AccountTransaction::groupBy('voucher_type')->orderBy('voucher_type', 'asc')->get();
        return $dataTable->render('admin.reports.voucher_list.index', compact('title', 'voucher_types'));
    }

    public function cashBook(Request $request)
    {
        $previousBalance = 0;
        $data = array();
        $coa_setup_id = $request->coa_setup_id;
        $date_range = explode('to', $request->date_range);
        $start_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
        $end_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');
        if ($request->has('filter')) {
            if (!is_null($request->coa_setup_id)) {
                $previousBalance = AccountTransaction::when($request->project_id, function ($query) use ($request) {
                    $query->where('project_id', $request->project_id);
                })->where('coa_setup_id', $request->coa_setup_id)->where('voucher_date', '<', $start_date)->sum(DB::raw('debit_amount - credit_amount'));
                $data = AccountTransaction::when($request->project_id, function ($query) use ($request) {
                    $query->where('project_id', $request->project_id);
                })->where('coa_setup_id', $request->coa_setup_id)->where('voucher_date', '>=', $start_date)->where('voucher_date', '<=', $end_date)->orderBy('voucher_date', 'asc')->get();
            }
        }

        if (!is_null($request->print)) {
            $report_title = 'Cash book Report <br> <span class="text-sm">' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '</span>';
            $pdf = Pdf::loadView('admin.reports.cash_book.print', compact('report_title', 'previousBalance', 'data'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('cash_book_' . date('d_m_Y_h_i_s') . '.pdf');
        }

        $title = 'Cash Book';
        $coas = CoaSetup::where('transaction', 1)->where('head_code', 'LIKE', '10102%')->orderBy('head_name', 'asc')->get();
        return view('admin.reports.cash_book.index', compact('title', 'coas', 'coa_setup_id', 'start_date', 'end_date', 'previousBalance', 'data'));
    }

    public function bankBook(Request $request)
    {
        $previousBalance = 0;
        $data = array();
        $date_range = explode('to', $request->date_range);
        $start_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
        $end_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');
        if ($request->has('filter')) {
            if (!is_null($request->coa_setup_id)) {
                $previousBalance = AccountTransaction::when($request->project_id, function ($query) use ($request) {
                    $query->where('project_id', $request->project_id);
                })->where('coa_setup_id', $request->coa_setup_id)->where('voucher_date', '<', $start_date)->sum(DB::raw('debit_amount - credit_amount'));
                $data = AccountTransaction::when($request->project_id, function ($query) use ($request) {
                    $query->where('project_id', $request->project_id);
                })->where('coa_setup_id', $request->coa_setup_id)->where('voucher_date', '>=', $start_date)->where('voucher_date', '<=', $end_date)->orderBy('voucher_date', 'asc')->get();
            }
        }

        if (!is_null($request->print)) {
            $report_title = 'Bank book Report <br> <span class="text-sm">' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '</span>';
            $pdf = Pdf::loadView('admin.reports.bank_book.print', compact('report_title', 'previousBalance', 'data'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('bank_book_' . date('d_m_Y_h_i_s') . '.pdf');
        }

        $title = 'Bank Book';
        $coas = CoaSetup::where('transaction', 1)->where('head_code', 'LIKE', '10103%')->orderBy('head_name', 'asc')->get();
        return view('admin.reports.bank_book.index', compact('title', 'coas', 'start_date', 'end_date', 'previousBalance', 'data'));
    }

    public function transactionLedger(Request $request)
    {
        $previousBalance = 0;
        $data = array();
        $date_range = explode('to', $request->date_range);
        $start_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
        $end_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');
        if ($request->has('filter')) {
            if (!is_null($request->coa_setup_id)) {
                $previousBalance = AccountTransaction::when($request->project_id, function ($query) use ($request) {
                    $query->where('project_id', $request->project_id);
                })->where('coa_setup_id', $request->coa_setup_id)->where('voucher_date', '<', $start_date)->sum(DB::raw('debit_amount - credit_amount'));
                $data = AccountTransaction::when($request->project_id, function ($query) use ($request) {
                    $query->where('project_id', $request->project_id);
                })->where('coa_setup_id', $request->coa_setup_id)->where('voucher_date', '>=', $start_date)->where('voucher_date', '<=', $end_date)->orderBy('voucher_date', 'desc')->get();
            }
        }

        if (!is_null($request->print)) {
            $report_title = 'Transaction Ledger Report <br> <span class="text-sm">' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '</span>';
            $pdf = Pdf::loadView('admin.reports.transaction_ledger.print', compact('report_title', 'previousBalance', 'data'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('transaction_ledger_' . date('d_m_Y_h_i_s') . '.pdf');
        }

        $title = 'Transaction Ledger';
        $coas = CoaSetup::where('transaction', 1)->orderBy('head_name', 'asc')->get();
        return view('admin.reports.transaction_ledger.index', compact('title', 'coas', 'start_date', 'end_date', 'previousBalance', 'data'));
    }

    public function cashFlowStatement(Request $request)
    {
        $data = array();
        $date_range = explode('to', $request->date_range);
        $start_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
        $end_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');
        $generalLedgerHeadCash = 10102;
        $generalLedgerHeadBank = 10103;

        if ($request->has('filter')) {
            $data = AccountTransaction::when($request->project_id, function ($query) use ($request) {
                $query->where('project_id', $request->project_id);
            })->where('voucher_date', '>=', $start_date)->where('voucher_date', '<=', $end_date)->groupBy('voucher_date')->orderBy('voucher_date', 'desc')->get('voucher_date');
        }

        if (!is_null($request->print)) {
            $report_title = 'Cash Flow Statement <br> <span class="text-sm">' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '</span>';;
            $pdf = Pdf::loadView('admin.reports.cash_flow_statement.print', compact('report_title', 'data', 'generalLedgerHeadCash', 'generalLedgerHeadBank'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('cash_flow_statement_' . date('d_m_Y_h_i_s') . '.pdf');
        }

        $title = 'Cash Flow Statement';
        return view('admin.reports.cash_flow_statement.index', compact('title', 'start_date', 'end_date', 'data', 'generalLedgerHeadCash', 'generalLedgerHeadBank'));
    }

    public function generalLedger(Request $request)
    {
        $previousBalance = 0;
        $data = array();
        $date_range = explode('to', $request->date_range);
        $start_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
        $end_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');
        if ($request->has('filter')) {
            if (!is_null($request->coa_head_code)) {
                $previousBalance = AccountTransaction::when($request->project_id, function ($query) use ($request) {
                    $query->where('project_id', $request->project_id);
                })->where('coa_head_code', 'LIKE', $request->coa_head_code . '%')->where('voucher_date', '<', $start_date)->sum(DB::raw('debit_amount - credit_amount'));
                $data = AccountTransaction::when($request->project_id, function ($query) use ($request) {
                    $query->where('project_id', $request->project_id);
                })->where('coa_head_code', 'LIKE', $request->coa_head_code . '%')->where('voucher_date', '>=', $start_date)->where('voucher_date', '<=', $end_date)->orderBy('voucher_date', 'desc')->get();
            }
        }

        if (!is_null($request->print)) {
            $report_title = 'General Ledger Report <br> <span class="text-sm">' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '</span>';
            $pdf = Pdf::loadView('admin.reports.general_ledger.print', compact('report_title', 'previousBalance', 'data'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('general_ledger_' . date('d_m_Y_h_i_s') . '.pdf');
        }

        $title = 'General Ledger';
        $coas = CoaSetup::where('general', 1)->orderBy('head_name', 'asc')->get();
        return view('admin.reports.general_ledger.index', compact('title', 'coas', 'start_date', 'end_date', 'previousBalance', 'data'));
    }

    public function incomeStatement(Request $request)
    {
        $date_range = explode('to', $request->date_range);
        $start_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
        $end_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');

        $incomes = array();
        $expenses = array();
        if ($request->has('filter')) {
            $incomes = AccountTransaction::with('coa')
                ->when($request->project_id, function ($query) use ($request) {
                    $query->where('project_id', $request->project_id);
                })
                ->where('voucher_date', '>=', $start_date)
                ->where('voucher_date', '<=', $end_date)
                ->whereHas('coa', function ($query) {
                    $query->where('head_type', 'I');
                })
                ->groupBy('coa_setup_id')
                ->select('coa_head_code', 'coa_setup_id', DB::raw('SUM(debit_amount) as debit_amount'), DB::raw('SUM(credit_amount) as credit_amount'))
                ->get();

            $expenses = AccountTransaction::with('coa')->select('*', DB::raw('SUM(debit_amount - credit_amount) as amount'))
                ->when($request->project_id, function ($query) use ($request) {
                    $query->where('project_id', $request->project_id);
                })
                ->where('voucher_date', '>=', $start_date)
                ->where('voucher_date', '<=', $end_date)
                ->whereHas('coa', function ($query) {
                    $query->where('head_type', 'E')->where('transaction', 1);
                })
                ->groupBy('coa_setup_id')
                ->get();
        }

        if (!is_null($request->print)) {
            $report_title = 'Income Statement Report <br> <span class="text-sm">' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '</span>';
            $pdf = Pdf::loadView('admin.reports.income_statement.print', compact('report_title', 'incomes', 'expenses'));
            return $pdf->stream('income_statement_' . date('d_m_Y_h_i_s') . '.pdf');
        }

        $title = 'Income Statement';
        $buttons = '<button type="button" class="btn btn-sm btn-primary text-uppercase getPdf">Print</button><button type="submit" class="btn btn-sm btn-primary text-uppercase" id="filter_btn">Search</button>';
        return view('admin.reports.income_statement.index', compact('title', 'start_date', 'end_date', 'incomes', 'expenses', 'buttons'));
    }

    public function incomeStatementHeadDetails(Request $request)
    {
        $title = 'Head Transactions';
        $data = AccountTransaction::with('coa')
            ->where('voucher_date', '>=', $request->start_date)
            ->where('voucher_date', '<=', $request->end_date)
            ->where('coa_setup_id', $request->coa_setup_id)
            ->get();

        if ($request->has('print')) {
            $report_title = 'Head Transactions Details <br> <span class="text-sm">' . date('d-m-Y', strtotime($request->start_date)) . ' To ' . date('d-m-Y', strtotime($request->end_date)) . '</span>';
            $pdf = Pdf::loadView('admin.reports.income_statement.details_print', compact('report_title', 'data'));
            return $pdf->stream('transaction_details' . date('d_m_Y_h_i_s') . '.pdf');
        }
        return view('admin.reports.income_statement.details', compact('title', 'data'));
    }

    public function trialBalance(Request $request)
    {
        $coaLists = array();
        $coaLists1 = array();
        $date_range = explode('to', $request->date_range);
        $start_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
        $end_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');
        if ($request->has('filter')) {
            $coaLists = TrialBalance::with('coa_setup')->when($request->project_id, function ($query) use ($request) {
                $query->where('project_id', $request->project_id);
            })->where('voucher_date', '>=', $start_date)->where('voucher_date', '<=', $end_date)->where('transaction', 1)->where('general', 1)->groupBy('coa_setup_id')->select('*', DB::raw('SUM(debit_amount) as debit_amount'), DB::raw('SUM(credit_amount) as credit_amount'))->get();
            $coaLists1 = TrialBalance::with('parent_head')->when($request->project_id, function ($query) use ($request) {
                $query->where('project_id', $request->project_id);
            })->where('voucher_date', '>=', $start_date)->where('voucher_date', '<=', $end_date)->where('transaction', 1)->where('general', 0)->groupBy('parent_id')->select('*', DB::raw('SUM(debit_amount) as debit_amount'), DB::raw('SUM(credit_amount) as credit_amount'))->get();
        }

        if (!is_null($request->print)) {
            $report_title = 'Trial Balance <br> <span class="text-sm">' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '</span>';
            $pdf = Pdf::loadView('admin.reports.trial_balance.print', compact('report_title', 'coaLists', 'coaLists1'));
            return $pdf->stream('trial_balance_' . date('d_m_Y_h_i_s') . '.pdf');
        }

        $title = 'Trial Balance';
        return view('admin.reports.trial_balance.index', compact('title', 'start_date', 'end_date', 'coaLists', 'coaLists1'));
    }


    public function balanceSheet(Request $request)
    {
        $assets = $this->assets('Assets');
        $liabilities = $this->assets('Liabilities');
        $currentPFL = $this->profitLoss();
        $data = [
            'assets' => $assets,
            'liabilities' => $liabilities,
            'currentPFL' => $currentPFL
        ];

        if (!is_null($request->print)) {
            if (Auth::user()->company_id) {
                $company = Company::find(Auth::user()->company_id);
                $title = $company->name;
                $informations = $company->address . '</br>' . $company->phone . ', ' . $company->email . ', ' . $company->website;
            } else {
                $title = 'Company Name Goes Here.';
                $informations = 'Company address will goes here </br> Mobile: 0967XXXXXX, Email: youremail@gmail.com, www.website.com';
            }

            $report_title = 'Balance Sheet';
            // return view('admin.reports.balance_sheet.print', compact('title', 'informations', 'report_title', 'data'));
            $pdf = Pdf::loadView('admin.reports.balance_sheet.print', compact('title', 'informations', 'report_title', 'data'));
            $pdf->setPaper('A4', 'landscape');
            return $pdf->stream('balance_sheet_' . date('d_m_Y_h_i_s') . '.pdf');
        }

        $title = 'Balance Sheet';
        $filter_link = Route('admin.balance-sheet.index');
        $coas = CoaSetup::where('general', 1)->orderBy('head_name', 'asc')->get();
        return view('admin.reports.balance_sheet.index', compact('title', 'filter_link', 'coas', 'data'));
    }

    public function balanceSheetHeadDetails(Request $request)
    {
        if ($request->has('coa_setup_id')) {
            $title = 'Head Transactions';
            $data = AccountTransaction::with('coa')
                ->where('coa_setup_id', $request->coa_setup_id)
                ->orderBy('id', 'desc')
                ->get();

            if ($request->has('print')) {
                $report_title = 'Head Transactions Details';
                $pdf = Pdf::loadView('admin.reports.balance_sheet.details_print', compact('report_title', 'data'));
                return $pdf->stream('transaction_details' . date('d_m_Y_h_i_s') . '.pdf');
            }
            return view('admin.reports.balance_sheet.details', compact('title', 'data'));
        }

        $title = 'Head Transactions';
        $coa_ids = CoaSetup::where('parent_id', $request->id)->pluck('id')->toArray();
        $child_coa_ids = CoaSetup::whereIn('parent_id', $coa_ids)->pluck('id')->toArray();
        $coa_ids = array_merge($coa_ids, $child_coa_ids);
        
        $data = AccountTransaction::with('coa')
            ->select(
                'coa_setup_id',
                DB::raw('SUM(debit_amount) as debit_amount'),
                DB::raw('SUM(credit_amount) as credit_amount')
            )
            ->whereIn('coa_setup_id', $coa_ids)
            ->groupBy('coa_setup_id')
            ->get();

            
            // dd($data);
        if ($request->has('print')) {
            $report_title = 'Head Transactions Details';
            $pdf = Pdf::loadView('admin.reports.balance_sheet.details_print', compact('report_title', 'data'));
            return $pdf->stream('transaction_details' . date('d_m_Y_h_i_s') . '.pdf');
        }
        return view('admin.reports.balance_sheet.details', compact('title', 'data'));
    }

    public function viewVoucher(string $id)
    {
        $title = "View Voucher";
        $transaction = AccountTransaction::findOrFail($id);
        $transactions = AccountTransaction::where('voucher_no', $transaction->voucher_no)
            ->where('voucher_type', $transaction->voucher_type)->get();
        return view('admin.reports.income_statement.view')->with(compact('title', 'transaction', 'transactions'));
    }


    public function getAmount($headCode)
    {
        $balance = 0;
        // match exact head code only (do not use prefix wildcard here)
        $headReports = TrialBalance::where('coa_head_code', $headCode)
            ->select('*')
            ->get();

        foreach ($headReports as $headReport) {
            if ($headReport->head_type == 'I' || $headReport->head_type == 'L') {
                $balance += $headReport->credit_amount - $headReport->debit_amount;
            } else {
                $balance += $headReport->debit_amount - $headReport->credit_amount;
            }
        }
        return $balance;
    }

    public function assets($parent_head)
    {
        $parents = CoaSetup::with('parent')->whereHas('parent', function ($query) use ($parent_head) {
            $query->where('head_name', $parent_head);
        })->get();
        $info = [];
        foreach ($parents as $parent) {
            // build infinite nested child info recursively
            $childInfo = $this->childs($parent->id);

            $info[] = [
                'id' => $parent->id,
                'headCode' => $parent->head_code,
                'head' => $parent->head_name,
                'childs' => $childInfo
            ];
        }
        return $info;
    }

    /**
     * Recursively build child tree for a given parent id.
     * Returns array of nodes with id, headCode, name, amount, and childs.
     */
    private function childs($parent_id)
    {
        $result = [];
        $childs = CoaSetup::select('head_name', 'head_code', 'id')
            ->where('parent_id', $parent_id)
            ->get();

        foreach ($childs as $child) {
            $result[] = [
                'id' => $child->id,
                'headCode' => $child->head_code,
                'name' => $child->head_name,
                'amount' => $this->getAmount($child->head_code),
                'childs' => $this->childs($child->id)
            ];
        }

        return $result;
    }

    private function profitLoss()
    {
        $totalIncome = trialBalance::where('head_type', 'I')->sum(DB::raw('credit_amount - debit_amount'));
        $totalExpanse = trialBalance::where('head_type', 'E')->sum(DB::raw('debit_amount - credit_amount'));
        return $totalIncome - $totalExpanse;
    }

    public function receivePayment(Request $request)
    {
        $date_range = explode('to', $request->date_range);
        $start_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[0])) : date('Y-m-01');
        $end_date = !is_null($request->date_range) ? date('Y-m-d', strtotime($date_range[1])) : date('Y-m-t');

        $data = array();
        if ($request->has('filter')) {
            $data = AccountTransaction::with('coa')
                ->when($request->project_id, function ($query) use ($request) {
                    $query->where('project_id', $request->project_id);
                })
                ->where('voucher_date', '>=', $start_date)
                ->where('voucher_date', '<=', $end_date)
                ->where(function ($q) {
                    $q->where('coa_head_code', 'LIKE', '10103%')
                        ->orWhere('coa_head_code', 'LIKE', '10102%');
                })
                ->groupBy('coa_setup_id')
                ->select('coa_head_code', 'coa_setup_id', DB::raw('SUM(debit_amount) as debit_amount'), DB::raw('SUM(credit_amount) as credit_amount'))
                ->get();
        }

        if (!is_null($request->print)) {
            $report_title = 'Receive Payment Report <br> <span class="text-sm">' . date('d-m-Y', strtotime($start_date)) . ' To ' . date('d-m-Y', strtotime($end_date)) . '</span>';
            $pdf = Pdf::loadView('admin.reports.recieve_payment.print', compact('report_title', 'data'));
            return $pdf->stream('recieve_payment_' . date('d_m_Y_h_i_s') . '.pdf');
        }

        $title = 'Receive Payment Report';
        $buttons = '<button type="button" class="btn btn-sm btn-primary text-uppercase getPdf">Print</button><button type="submit" class="btn btn-sm btn-primary text-uppercase" id="filter_btn">Search</button>';
        return view('admin.reports.recieve_payment.index', compact('title', 'start_date', 'end_date', 'data', 'buttons'));
    }

    public function receivePaymentHeadDetails(Request $request)
    {
        $title = 'Head Transactions';
        $data = AccountTransaction::with('coa')
            ->where('voucher_date', '>=', $request->start_date)
            ->where('voucher_date', '<=', $request->end_date)
            ->where('coa_setup_id', $request->coa_setup_id)
            ->get();

        if ($request->has('print')) {
            $report_title = 'Head Transactions Details <br> <span class="text-sm">' . date('d-m-Y', strtotime($request->start_date)) . ' To ' . date('d-m-Y', strtotime($request->end_date)) . '</span>';
            $pdf = Pdf::loadView('admin.reports.recieve_payment.details_print', compact('report_title', 'data'));
            return $pdf->stream('transaction_details' . date('d_m_Y_h_i_s') . '.pdf');
        }
        return view('admin.reports.recieve_payment.details', compact('title', 'data'));
    }
}
