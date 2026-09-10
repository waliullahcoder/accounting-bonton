<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Http\Controllers\Controller;
use App\Models\AccountTransactionAuto;
use App\Models\AdminSetting;
use App\Models\BillingMonth;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Models\ClientCollection;
use App\Models\ClientCollectionList;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ClientCollectionController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'client-collection';
        $this->title = 'Client Collections';
        $this->create_title = 'Client Collection';
        $this->edit_title = 'Update Collection';
        $this->model = ClientCollection::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $addition_btns = [
            'route' => 'admin.client-collection.show',
            'parameter' => true,
            'target' => '_self',
            'class' => 'btn btn-sm btn-primary tt mw-fit',
            'title' => 'View',
            'icon' => '<i class="fas fa-eye"></i>',
        ];
        return HelperClass::resourceDataView($this->model::orderBy('id', 'desc'), NULL, $addition_btns, $this->path, $this->title, 'transactions', NULL, 'conditional');
    }

    public function serialNo($month = NULL, $year = NULL)
    {
        if (is_null($month) && is_null($year)) {
            $first = date('Y-m-01');
            $last = date('Y-m-t');
            $data = $this->model::select(['serial_no'])->where('created_at', '>=', $first)->where('created_at', '<=', $last)->orderBy('id', 'desc')->first();
            return is_null($data) ? (date('ym') . '001') : ((int)$data->serial_no + 1);
        } else {
            $first = date('Y-m-01', strtotime($year . '-' . $month));
            $last = date('Y-m-t', strtotime($year . '-' . $month));
            $data = $this->model::select(['serial_no'])->where('created_at', '>=', $first)->where('created_at', '<=', $last)->orderBy('id', 'desc')->first();
            return is_null($data) ? (date('ym', strtotime($first)) . '001') : ((int)$data->serial_no + 1);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->model::where('month', $request->month)->where('year', $request->year)->first();
            $billingMonths = BillingMonth::with(['client', 'service'])->where('month', $request->month)->get();
            return response()->json([
                'serial_no' => $this->SerialNo($request->month, $request->year),
                'data' => view('admin.client-collection.partial.table', compact('data', 'billingMonths'))->render()
            ]);
        }

        $title = $this->create_title;
        $serial_no = $this->SerialNo();
        $billingMonths = BillingMonth::with(['client', 'service'])->where('month', date('F'))->get();
        return view("admin.{$this->path}.create", compact('title', 'serial_no', 'billingMonths'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'serial_no' => 'required',
            'month' => 'required',
            'year' => 'required',
            'month' => 'required',
            'date' => 'required',
            'service_id' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            $data = $this->model::where('year', $request->year)->where('month', $request->month)->first();
            if (!is_null($data)) {
                $data->update([
                    'month' => $request->month,
                    'year' => $request->year,
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'total_bill_amount' => $request->total_bill_amount,
                    'total_collection_amount' => $request->total_collection_amount,
                    'created_by' => Auth::user()->id,
                ]);
                $serial_no = $data->serial_no;

                ClientCollectionList::where('client_collection_id', $data->id)->delete();
                AccountTransactionAuto::where('voucher_type', 'Collection')->where('voucher_no', $data->serial_no)->forceDelete();
            } else {
                $serial_no = $this->SerialNo($request->month, $request->year);
                $data = $this->model::create([
                    'serial_no' => $serial_no,
                    'month' => $request->month,
                    'year' => $request->year,
                    'date' => date('Y-m-d', strtotime($request->date)),
                    'total_bill_amount' => $request->total_bill_amount,
                    'total_collection_amount' => $request->total_collection_amount,
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-01 H:i:s', strtotime($request->year . '-' . $request->month)),
                ]);
            }

            $admin_settings = AdminSetting::first();
            $postData[] = [
                'company_id' => Auth::user()->company_id ?? 1,
                'voucher_no' => $serial_no,
                'project_id' => $request->project_id,
                'voucher_type' => "Collection",
                'voucher_date' => date('Y-m-d', strtotime($request->date)),
                'coa_setup_id' => $admin_settings->collection_head,
                'coa_head_code' => @$admin_settings->coa->head_code,
                'narration' => 'Collection Against Serial No - ' . $serial_no,
                'debit_amount' => $request->total_collection_amount,
                'credit_amount' => 0,
                'document' => NULL,
                'created_by' => Auth::user()->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];

            foreach ($request->service_id as $service_id) {
				if($request->collection_amount[$service_id] > 0){
	                ClientCollectionList::create([
	                    'client_collection_id' => $data->id,
	                    'client_id' => $request->client_id[$service_id],
	                    'service_id' => $service_id,
	                    'month' => $request->month,
	                    'year' => $request->year,
	                    'bill_amount' => $request->bill_amount[$service_id],
	                    'collection_amount' => $request->collection_amount[$service_id],
	                ]);
	                $client = Client::find($request->client_id[$service_id]);
	                $postData[] = [
	                    'company_id' => Auth::user()->company_id ?? 1,
	                    'voucher_no' => $serial_no,
	                    'project_id' => $request->project_id,
	                    'voucher_type' => "Collection",
	                    'voucher_date' => date('Y-m-d', strtotime($request->date)),
	                    'coa_setup_id' => $client->coa_setup_id,
	                    'coa_head_code' => @$client->coa->head_code,
	                    'narration' => 'Collection Against Serial No - ' . $serial_no,
	                    'debit_amount' => 0,
	                    'credit_amount' => $request->collection_amount[$service_id],
	                    'document' => NULL,
	                    'created_by' => Auth::user()->id,
	                    'created_at' => Carbon::now(),
	                    'updated_at' => Carbon::now()
	                ];					
				}
            }
            AccountTransactionAuto::insert($postData);
        });

        return redirect()->route("admin.{$this->path}.index")->withSuccessMessage('Created Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        if ($request->has('print')) {
            $data = $this->model::findOrFail($id);
            $report_title = 'Client Bill <br> <span class="text-sm">' . $data->month . ' - ' . $data->year . '</span>';
            // return view("admin.client-collection.print", compact('report_title', 'data'));
            $pdf = Pdf::loadView("admin.client-collection.print", compact('report_title', 'data'));
            $pdf->setPaper('A4', 'potrait');
            return $pdf->stream('client_bill_' . date('d_m_Y_H_i_s') . '.pdf');
        }

        $title = 'View Client Collection';
        $data = $this->model::findOrFail($id);
        $billingMonths = BillingMonth::with(['client', 'service'])->where('month', $data->month)->get();
        return view('admin.client-collection.view', compact('title', 'data', 'billingMonths'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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
		$data = $this->model::findOrFail($id);
		ClientCollectionList::where('client_collection_id', $data->id)->delete();
		AccountTransactionAuto::where('voucher_type', 'Collection')->where('voucher_no', $data->serial_no)->forceDelete();
		$data->forceDelete();
        return response()->json(['status' => 'success']);
    }
}
