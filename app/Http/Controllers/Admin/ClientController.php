<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\CoaSetup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'client';
        $this->title = 'Clients List';
        $this->create_title = 'Add Client';
        $this->edit_title = 'Update Client';
        $this->model = Client::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return HelperClass::resourceDataView($this->model::with(['coa'])->orderBy('id', 'desc'), NULL, NULL, $this->path, $this->title, 'transactions');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if ($request->ajax()) {
            $client_coa_ids = Client::pluck('coa_setup_id')->toArray();
            $coas = CoaSetup::whereHas('parent', function ($query) use ($request) {
                $name = '';
                if ($request->type == 'Domain Hosting') {
                    $name = 'Domain & Hosting Bill';
                } elseif ($request->type == 'Digital Marketing') {
                    $name = 'Digital Marketing';
                } elseif ($request->type == 'Support Service') {
                    $name = 'Software Support Service';
                }
                $query->where('head_name', $name);
            })->whereNotIn('id', $client_coa_ids)->where('head_type', 'I')->where('transaction', 1)->get();
            return response()->json(['status' => 'success', 'coas' => $coas]);
        }

        $title = $this->create_title;
        return view("admin.{$this->path}.create", compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'coa_setup_id' => 'required'
        ]);

        $this->model::create([
            'name' => $request->name,
            'type' => $request->type,
            'company_name' => $request->company_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'coa_setup_id' => $request->coa_setup_id,
            'created_by' => Auth::user()->id,
        ]);

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
        if ($request->ajax()) {
            $data = $this->model::findOrFail($id);
            $client_coa_ids = Client::whereNot('id', $data->id)->pluck('coa_setup_id')->toArray();
            $coas = CoaSetup::whereHas('parent', function ($query) use ($request) {
                $name = '';
                if ($request->type == 'Domain Hosting') {
                    $name = 'Domain & Hosting Bill';
                } elseif ($request->type == 'Digital Marketing') {
                    $name = 'Digital Marketing';
                } elseif ($request->type == 'Support Service') {
                    $name = 'Software Support Service';
                }
                $query->where('head_name', $name);
            })->whereNotIn('id', $client_coa_ids)->where('head_type', 'I')->where('transaction', 1)->get();
            return response()->json(['status' => 'success', 'coas' => $coas]);
        }

        $title = $this->edit_title;
        return HelperClass::resourceDataEdit($this->model, $id, $this->path, $title);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
        ]);

        $data = $this->model::findOrFail($id);
        $data->update([
            'name' => $request->name,
            'type' => $request->type,
            'company_name' => $request->company_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'coa_setup_id' => $request->coa_setup_id ?? $data->coa_setup_id,
            'updated_by' => Auth::user()->id,
        ]);

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
