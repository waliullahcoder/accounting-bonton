<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Http\Controllers\Controller;
use App\Models\BillingMonth;
use App\Models\Client;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'service';
        $this->title = 'Client Services';
        $this->create_title = 'Add Service';
        $this->edit_title = 'Update Service';
        $this->model = Service::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return HelperClass::resourceDataView($this->model::with(['client'])->orderBy('id', 'desc'), NULL, NULL, $this->path, $this->title);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $title = $this->create_title;
        $clients = Client::where('status', 1)->orderBy('name', 'asc')->get();
        return view("admin.{$this->path}.create", compact('title', 'clients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required',
            'software_type' => 'required',
            'pay_type' => 'required',
            'month' => 'required',
            'service_charge' => 'required',
        ]);

        DB::transaction(function () use ($request) {
            $data = $this->model::create([
                'client_id' => $request->client_id,
                'software_type' => $request->software_type,
                'pay_type' => $request->pay_type,
                'service_charge' => $request->service_charge,
                'created_by' => Auth::user()->id,
            ]);

            foreach ($request->month as $month) {
                BillingMonth::create([
                    'service_id' => $data->id,
                    'client_id' => $request->client_id,
                    'month' => $month
                ]);
            }
        });

        return redirect()->route("admin.{$this->path}.index")->withSuccessMessage('Created Successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, string $id)
    {
        $title = $this->edit_title;
        $additionalData = [
            'clients' => Client::where('status', 1)->orderBy('name', 'asc')->get()
        ];
        return HelperClass::resourceDataEdit($this->model, $id, $this->path, $title, $additionalData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'client_id' => 'required',
            'software_type' => 'required',
            'pay_type' => 'required',
            'month' => 'required',
            'service_charge' => 'required',
        ]);

        DB::transaction(function () use ($request, $id) {
            $data = $this->model::findOrFail($id);
            $data->update([
                'client_id' => $request->client_id,
                'software_type' => $request->software_type,
                'pay_type' => $request->pay_type,
                'service_charge' => $request->service_charge,
                'updated_by' => Auth::user()->id,
            ]);

            BillingMonth::where('service_id', $id)->delete();
            foreach ($request->month as $month) {
                BillingMonth::create([
                    'service_id' => $data->id,
                    'client_id' => $request->client_id,
                    'month' => $month
                ]);
            }
        });

        return redirect()->route("admin.{$this->path}.index")->withSuccessMessage('Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return HelperClass::resourceDataDelete($this->model, $id);
    }
}
