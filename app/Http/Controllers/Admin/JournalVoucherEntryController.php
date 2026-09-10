<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Project;
use App\Models\Company;
use App\Models\CoaSetup;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Models\Scopes\CompanyScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\Controller;
use App\Models\AccountTransactionAuto;
use Yajra\DataTables\Facades\DataTables;
use App\Services\ActionButtons\ActionButtons;

class JournalVoucherEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $model = AccountTransactionAuto::with(['company', 'project'])
                ->where('voucher_type', 'JV')
                ->orderBY('id', 'desc')
                ->orderBY('voucher_date', 'desc')->groupBy('voucher_no');
            $type = request('type');
            if (!empty($type) && $type == 'trash') {
                $model->onlyTrashed();
            }
            return DataTables::eloquent($model)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    $checkbox = '<div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input ' . (!empty(request('type')) && request('type') == "trash" ? 'trash_multi_checkbox' : 'multi_checkbox') . '" id="' . $row->id . '" name="multi_checkbox[]" value="' . $row->id . '"><label for="' . $row->id . '" class="custom-control-label"></label></div>';
                    return $checkbox;
                })
                ->addColumn('voucher_date', function ($row) {
                    return date('d-m-Y', strtotime($row->voucher_date));
                })
                ->addColumn('debit_head', function ($row) {
                    return AccountTransactionAuto::with('coa')->where('voucher_no', $row->voucher_no)
                        ->where('voucher_type', 'JV')
                        ->where('debit_amount', '>', 0)
                        ->get('coa_setup_id')->pluck('coa.head_name')->toArray();
                })
                ->addColumn('credit_head', function ($row) {
                    return AccountTransactionAuto::with('coa')->where('voucher_no', $row->voucher_no)
                        ->where('voucher_type', 'JV')
                        ->where('credit_amount', '>', 0)
                        ->get('coa_setup_id')->pluck('coa.head_name')->toArray();
                })
                ->addColumn('approve', function ($row) {
                    return $row->approve == 1 ? 'Approved' : 'Pending';
                })
                ->addColumn('actions', function ($row) {
                    $type = request('type');
                    $data = [
                        'id' => $row->id,
                        'edit' => !empty($type) && $type == 'trash' ? false : true,
                    ];

                    $actionBtn = '<a class="btn btn-sm text-white tt btn-print-1" href="' . Route('admin.journal-voucher-entry.show', $row->id) . '" data-bs-toggle="tooltip" data-bs-placement="top" title="View"><i class="fas fa-eye"></i></a>';
                    $actionBtn .= '<a class="btn btn-sm text-white tt btn-print-2" href="' . Route('admin.journal-voucher-entry.print', $row->id) . '" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" title="Print"><i class="fal fa-print"></i></a>';

                    if ($type == 'all' && $row->approve == 1 || is_null($type) && $row->approve == 1) {
                        return '<div class="btn-group">' . $actionBtn . '</div>';
                    } else {
                        return ActionButtons::actions($data, $actionBtn);
                    }
                })
                ->rawColumns(['checkbox', 'actions'])
                ->make(true);
        }
        $title = 'Journal Voucher Entry';
        return view('admin.journal_voucher_entry.index', compact('title'));
    }

    public function voucherNo()
    {
        $str = "JV-";
        $serialNo = AccountTransactionAuto::withoutGlobalScope(CompanyScope::class)->withTrashed()->where('voucher_type', 'JV')->max('voucher_no');
        if ($serialNo) {
            $serialNo = substr($serialNo, strlen($str));
            $voucherNo = $str . ($serialNo + 1);
        } else {
            $voucherNo = $str . "1000000001";
        }
        return $voucherNo;
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::all();
        $title = 'Create Journal Voucher';
        $coas = CoaSetup::where('transaction', '1')->orderBy('head_name', 'asc')->get();
        $voucher_no = $this->voucherNo();
        return view('admin.journal_voucher_entry.create', compact('title', 'projects', 'coas', 'voucher_no'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'voucher_date' => 'required',
            'coa_id' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            $create_path = public_path('media/journal-voucher');
            if (!File::isDirectory($create_path)) {
                File::makeDirectory($create_path, 0777, true, true);
            }

            $file = $request->file('document');
            if (isset($file)) {
                $original_extension = $file->getClientOriginalExtension();
                $file_name = Carbon::now()->toDateString() . '-' . Str::random(40);
                $file_name = $file_name . '.' . $original_extension;
                $path = 'media/journal-voucher';
                $file->move($path, $file_name);
                $path_file_name = $path . '/' . $file_name;
            }

            foreach ($request->coa_id as $coa_id) {
                if ($request->debit_amount[$coa_id] == 0 && $request->credit_amount[$coa_id] == 0) {
                    continue;
                }
                $voucher_no = $this->voucherNo();

                $postData[] = [
                    'company_id' => Auth::user()->company_id ?? 1,
                    'voucher_no' => $voucher_no,
                    'project_id' => $request->project_id,
                    'voucher_type' => "JV",
                    'voucher_date' => date('Y-m-d', strtotime($request->voucher_date)),
                    'coa_setup_id' => $coa_id,
                    'coa_head_code' => $request->head_code[$coa_id],
                    'narration' => $request->narration,
                    'debit_amount' => $request->debit_amount[$coa_id] ?? 0,
                    'credit_amount' => $request->credit_amount[$coa_id] ?? 0,
                    'document' => @$path_file_name,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }
            AccountTransactionAuto::insert($postData);
        });

        return redirect()->route('admin.journal-voucher-entry.index')->withSuccessMessage('Created Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $title = "View Journal Voucher Entry";
        $journalEntry = AccountTransactionAuto::findOrFail($id);
        $journalEntries = AccountTransactionAuto::with('coa')->where('voucher_no', $journalEntry->voucher_no)
            ->where('voucher_type', 'JV')
            ->get();

        return view('admin.journal_voucher_entry.view')->with(compact('title', 'journalEntry', 'journalEntries'));
    }

    public function print(string $id)
    {
        if (Auth::user()->company_id) {
            $company = Company::find(Auth::user()->company_id);
            $title = $company->name;
            $informations = $company->address . '</br>' . $company->phone . ', ' . $company->email . ', ' . $company->website;
        } else {
            $title = 'Company Name Goes Here.';
            $informations = 'Company address will goes here </br> Mobile: 0967XXXXXX, Email: youremail@gmail.com, www.website.com';
        }
        $journalEntry = AccountTransactionAuto::findOrFail($id);
        $journalEntries = AccountTransactionAuto::with('coa')->where('voucher_no', $journalEntry->voucher_no)
            ->where('voucher_type', 'JV')
            ->get();

        $report_title = 'Journal Voucher';
        return view('admin.journal_voucher_entry.print', compact('title', 'informations', 'report_title', 'journalEntry', 'journalEntries'));
        $pdf = Pdf::loadView('admin.journal_voucher_entry.print', compact('title', 'informations', 'report_title', 'journalEntry', 'journalEntries'));
        return $pdf->stream('journal_voucher_print_' . date('d_m_Y_h_i_s') . '.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = AccountTransactionAuto::findOrFail($id);
        $journalEntries = AccountTransactionAuto::with('coa')->where('voucher_no', $data->voucher_no)
            ->where('voucher_type', 'JV')
            ->get();
        $projects = Project::all();
        $title = "Update Journal Voucher";
        $link = Route('admin.journal-voucher-entry.update', $id);
        $coas = CoaSetup::whereNotIn('id', $journalEntries->pluck('coa_setup_id')->toArray())->where('transaction', '1')->orderBy('head_name', 'asc')->get();
        return view('admin.journal_voucher_entry.edit', compact('title', 'projects', 'link', 'data', 'journalEntries', 'coas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'voucher_date' => 'required',
            'coa_id' => 'required',
        ]);

        DB::transaction(function () use ($request, $id) {
            $data = AccountTransactionAuto::findOrFail($id);

            $path_file_name = $data->document;
            $file = $request->file('document');
            if (isset($file)) {
                $original_extension = $file->getClientOriginalExtension();
                $file_name = Carbon::now()->toDateString() . '-' . Str::random(40);
                $file_name = $file_name . '.' . $original_extension;
                $path = 'media/journal-voucher';
                $file->move($path, $file_name);
                $path_file_name = $path . '/' . $file_name;
                if (file_exists($data->document)) {
                    unlink($data->document);
                }
            }

            AccountTransactionAuto::with('coa')->where('voucher_no', $data->voucher_no)
                ->where('voucher_type', 'JV')
                ->forceDelete();

            foreach ($request->coa_id as $coa_id) {
                if ($request->debit_amount[$coa_id] == 0 && $request->credit_amount[$coa_id] == 0) {
                    continue;
                }
                $voucher_no = $this->voucherNo();
                $postData[] = [
                    'company_id' => Auth::user()->company_id ?? 1,
                    'voucher_no' => $voucher_no,
                    'project_id' => $request->project_id,
                    'voucher_type' => "JV",
                    'voucher_date' => date('Y-m-d', strtotime($request->voucher_date)),
                    'coa_setup_id' => $coa_id,
                    'coa_head_code' => $request->head_code[$coa_id],
                    'narration' => $request->narration,
                    'debit_amount' => $request->debit_amount[$coa_id] ?? 0,
                    'credit_amount' => $request->credit_amount[$coa_id] ?? 0,
                    'document' => @$path_file_name,
                    'created_by' => Auth::user()->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }
            AccountTransactionAuto::insert($postData);
        });

        return redirect()->route('admin.journal-voucher-entry.index')->withSuccessMessage('Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Recovery Deleted Data
        if (request()->has('recovery') && request('recovery') == 'true') {
            $data = AccountTransactionAuto::onlyTrashed()->findOrFail($id);
            AccountTransactionAuto::with('coa')->where('voucher_no', $data->voucher_no)
                ->where('voucher_type', 'JV')
                ->restore();
            $data->restore();
            return response()->json(['status' => 'success']);
        }

        // Delete Single Item Permanent
        if (request()->has('parmanent') && request('parmanent') == 'true') {
            $data = AccountTransactionAuto::onlyTrashed()->findOrFail($id);
            AccountTransactionAuto::with('coa')->where('voucher_no', $data->voucher_no)
                ->where('voucher_type', 'JV')
                ->forceDelete();
            if (file_exists($data->document)) {
                unlink($data->document);
            }
            $data->forceDelete();
            return response()->json(['status' => 'success']);
        }

        // Delete Single Item
        $data = AccountTransactionAuto::findOrFail($id);
        AccountTransactionAuto::with('coa')->where('voucher_no', $data->voucher_no)
            ->where('voucher_type', 'JV')
            ->update(['deleted_by' => Auth::user()->id]);
        AccountTransactionAuto::with('coa')->where('voucher_no', $data->voucher_no)
            ->where('voucher_type', 'JV')
            ->delete();

        $data->update(['deleted_by' => Auth::user()->id]);
        $data->delete();

        return response()->json(['status' => 'success']);
    }
}
