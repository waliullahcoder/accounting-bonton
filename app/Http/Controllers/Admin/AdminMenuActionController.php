<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminMenuAction;
use App\Services\ActionButtons\ActionButtons;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Artisan;

class AdminMenuActionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($id)
    {
        if (request()->ajax()) {
            $model = AdminMenuAction::query()->where('admin_menu_id', $id)->with(['parent'])->latest();
            return DataTables::eloquent($model)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $status = '<div class="form-check form-switch">
                    <input class="form-check-input change-status c-pointer" data-url="' . Route('admin.admin-menuAction.edit', $row->id) . '" type="checkbox" name="status" ' . ($row->status == 1 ? 'checked' : '') . '>
                    </div>';
                    return $status;
                })
                ->addColumn('actions', function ($row) {
                    $data = [
                        'id' => $row->id,
                        'edit' => true,
                    ];
                    return ActionButtons::actions($data);
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }
        return view('admin.admin_menu_action.index', compact('id'));
    }

    private function cacheClear()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('config:cache');
        Artisan::call('view:clear');
        Artisan::call('route:clear');
        Artisan::call('clear-compiled');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        return view('admin.admin_menu_action.create', compact('id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required',
            'route' => 'required', 'unique:admin_menu_actions,route'
        ]);

        $check_permission = Permission::where('name', $request->route)->first();
        if ($check_permission) {
            return redirect()->back()->withErrors('Action Already Added');
        }

        $permission = Permission::create(['name' => $request->route]);
        $role = Role::findByName('Software Admin');
        $role->givePermissionTo($permission);

        AdminMenuAction::create([
            'permission_id' => $permission->id,
            'admin_menu_id' => $id,
            'name' => $request->name,
            'route' => $request->route,
            'status' => $request->status,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $this->cacheClear();
        return redirect()->back()->withSuccessMessage('Created Succcessfully!');
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
        if (request()->ajax() && request('status')) {
            $data = AdminMenuAction::findOrFail($id);
            $data->update(['status' => !$data->status]);
            return response()->json(['status' => 'success']);
        }
        $action = AdminMenuAction::findOrFail($id);
        return view('admin.admin_menu_action.edit', compact('action'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'  => 'required',
            'route' => ['required', 'unique:admin_menu_actions,route,' . $id]
        ]);

        $action = AdminMenuAction::findOrFail($id);
        if ($action->route !== $request->route) {
            $check_permission = Permission::where('name', $request->route)->first();
            if ($check_permission) {
                return redirect()->back()->withErrors('Action Already Added');
            }
        }

        Permission::where('id', $action->permission_id)->update(['name' => $request->route]);

        $action->name = $request->name;
        $action->route = $request->route;
        $action->status = $request->status;
        $action->updated_at = Carbon::now();
        $action->save();
        $this->cacheClear();
        return redirect()->route('admin.admin-menuAction.index', $action->admin_menu_id)->withSuccessMessage('Updated Succcessfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $action = AdminMenuAction::findOrFail($id);
        Permission::findById($action->permission_id)->delete();
        $action->delete();

        $this->cacheClear();
        return response()->json(['status' => 'success']);
    }
}
