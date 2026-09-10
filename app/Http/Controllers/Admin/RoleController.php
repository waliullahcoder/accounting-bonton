<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminMenu;
use App\Services\ActionButtons\ActionButtons;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $model = Role::latest('id')->whereNotIn('name', ['Software Admin']);
            if (Auth::user()->company_id) {
                $model->where('company_id', Auth::user()->company_id);
            }
            if (!Auth::user()->hasRole('Software Admin')) {
                $model->whereNotIn('name', ['System Admin', 'Staff', 'Investor', 'Delivery Man']);
            }
            return DataTables::eloquent($model)
                ->addColumn('checkbox', function ($row) {
                    $checkbox = '<div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input multi_checkbox" id="' . $row->id . '" name="multi_checkbox[]" value="' . $row->id . '"><label for="' . $row->id . '" class="custom-control-label"></label></div>';
                    return $checkbox;
                })
                ->addColumn('actions', function ($row) {
                    $data = [
                        'id' => $row->id,
                        'edit' => true,
                    ];
                    $addiotional_buttons = '';
                    $addiotional_buttons .= '<a href="' . Route('admin.rolePermission.edit', $row->id) . '" class="btn btn-sm btn-success border-0 px-10px fs-15 link-edit"><i class="far fa-lock-alt"></i></a>';
                    return ActionButtons::actions($data, $addiotional_buttons);
                })
                ->rawColumns(['checkbox', 'actions'])
                ->make(true);
        }
        $title = 'Role Setup';
        return view('admin.role.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'Role Information';
        return view('admin.role.create', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'unique:roles,name']
        ]);
        Role::create(['name' => $request->name, 'company_id' => Auth::user()->company_id]);
        return redirect()->route('admin.role.index')->withSuccessMessage('Created Successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = Role::findById($id);
        $title = 'Update Role';
        return view('admin.role.edit', compact('data', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required', 'unique:roles,name,' . $id]
        ]);
        $role = Role::findById($id);
        $role->name = $request->name;
        $role->save();
        return redirect()->route('admin.role.index')->withSuccessMessage('Update Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        // Delete Multiple Items
        if ($request->id) {
            foreach ($request->id as $id) {
                $role = Role::findById($id);
                $role->syncPermissions([]);
                $role->delete();
            }
            return response()->json(['status' => 'success']);
        }

        // Delete Single Item
        $role = Role::findById($id);
        $role->syncPermissions([]);
        $role->delete();
        return response()->json(['status' => 'success']);
    }

    public function rolePermissionEdit($id)
    {
        $role = Role::findById($id);
        $decoded_permission_id = json_decode($role->permissions);
        $permission_id = array_column($decoded_permission_id, 'id');
        $root_menus = AdminMenu::root()->with(['children', 'actions', 'permission'])->where('status', 1)->orderBy('order', 'asc')->get();
        return view('admin.role.role_permission', compact('role', 'permission_id', 'root_menus'));
    }

    public function rolePermissionUpdate(Request $request, $id)
    {
        $role = Role::findById($id);
        $role->permissions()->detach();
        $permission_ids = $request->permission_id;

        if (!empty($permission_ids)) {
            $permissions = Permission::whereIn('id', $permission_ids)->get();
            $role->syncPermissions($permissions);
        }
        return redirect()->back()->withSuccessMessage('Updated Successfully!');
    }
}
