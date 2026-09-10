<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
    public $path;
    public $title;
    public $create_title;
    public $edit_title;
    public $model;
    public function __construct()
    {
        $this->path = 'company';
        $this->title = 'Company Setup';
        $this->create_title = 'Add Company';
        $this->edit_title = 'Update Company';
        $this->model = Company::class;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return HelperClass::resourceDataView($this->model::orderBy('id', 'desc'), 'logo', NULL, $this->path, $this->title);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
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
            'username' => 'required|unique:users,user_name',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|unique:users,phone',
            'logo' => 'image|required',
        ]);

        DB::transaction(function () use ($request) {
            $data = Company::create([
                'prefix' => $request->prefix,
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'fax' => $request->fax,
                'website' => $request->website,
                'vat' => $request->vat,
                'tin' => $request->tin,
                'trade_license' => $request->trade_license,
                'address' => $request->address,
                'logo' =>  isset($request->logo) ? HelperClass::saveImage($request->logo, 500, 'media/' . $this->path) : NULL,
                'created_by' => Auth::user()->id,
            ]);

            // create user
            $user = User::create([
                'role' => 1,
                'company_id' => $data->id,
                'name' => $request->name,
                'user_name' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => bcrypt(12345678),
                'created_by' => Auth::user()->id,
            ]);

            $data->update([
                'user_id' => $user->id
            ]);

            $role = Role::findByName('System Admin');
            $user->assignRole($role);
        });

        return redirect()->route('admin.company.index')->withSuccessMessage('Created Successfully!');
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
    public function edit(string $id)
    {
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
            'username' => 'required|unique:companies,username,' . $id,
            'email' => 'required|email|unique:companies,email,' . $id,
            'phone' => 'required|unique:companies,phone,' . $id,
            'email' => 'required|email',
            'phone' => 'required',
            'logo' => 'image',
        ]);

        DB::transaction(function () use ($request, $id) {
            $data = Company::findOrFail($id);
            $data->update([
                'prefix' => $request->prefix,
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'fax' => $request->fax,
                'website' => $request->website,
                'vat' => $request->vat,
                'tin' => $request->tin,
                'trade_license' => $request->trade_license,
                'address' => $request->address,
                'logo' => isset($request->logo) ? HelperClass::saveImage($request->logo, 500, 'media/' . $this->path, $data->logo) : $data->logo,
                'updated_by' => Auth::user()->id,
            ]);

            $user = User::findOrFail($data->user_id);
            $user->update([
                'name' => $request->name,
                'user_name' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'updated_by' => Auth::user()->id,
            ]);
        });

        return redirect()->route('admin.company.index')->withSuccessMessage('Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id) {}
}
