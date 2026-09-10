<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $model = User::with(['company'])->whereIn('role', [1])->whereNotIn('id', [Auth::user()->id])->orderBy('id', 'desc');
            $company_id = Auth::user()->company_id;
            if ($company_id) {
                $model->where('company_id', $company_id);
            }
            $type = request('type');
            if (!empty($type) && $type == 'trash') {
                $model->onlyTrashed();
            }
            $model->whereNotIn('user_name', ['admin']);
            return DataTables::eloquent($model)
                ->addColumn('checkbox', function ($row) {
                    $checkbox = '<div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input ' . (!empty(request('type')) && request('type') == "trash" ? 'trash_multi_checkbox' : 'multi_checkbox') . '" id="' . $row->id . '" name="multi_checkbox[]" value="' . $row->id . '"><label for="' . $row->id . '" class="custom-control-label"></label></div>';
                    return $checkbox;
                })
                ->addColumn('image', function ($row) {
                    $image = '<img class="lazyload" data-src="' . (file_exists($row->image) ? asset($row->image) : asset('backend/images/avatar/default/user.jpg')) . ' " height="40" alt="' . $row->name . '">';
                    return $image;
                })
                ->addColumn('role', function ($row) {
                    return @$row->getRoleNames()->toArray();
                })
                ->addColumn('status', function ($row) {
                    $status = '<div class="form-check form-switch">
                    <input class="form-check-input change-status c-pointer" data-url="' . Route('admin.user.edit', $row->id) . '" type="checkbox" name="status" ' . ($row->status == 1 ? 'checked' : '') . '>
                    </div>';
                    return $status;
                })
                ->addColumn('actions', function ($row) {
                    $actionBtn = '<div class="btn-group">';
                    $type = request('type');
                    if (!empty($type) && $type == 'trash') {
                        $actionBtn .= '<button type="button" class="btn btn-sm tt btn-success link-recovery" data-url="' . Route('admin.user.destroy', $row->id) . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Recovery"><i class="fad fa-recycle"></i></button>';
                    } else {
                        if ($row->role == 1) {
                            if (!in_array('Software Admin', json_decode($row->getRoleNames())) && Auth::user()->hasRole('Software Admin') || Auth::user()->can('admin.user.edit') && !in_array('Software Admin', json_decode($row->getRoleNames()))) {
                                $actionBtn .= '<a href="' . Route('admin.user.edit', $row->id) . '" class="btn btn-sm btn-warning border-0 px-10px fs-15 tt link-edit" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit"><i class="far fa-pencil-alt"></i></a>';
                            }
                        }
                        if (!in_array('Software Admin', json_decode($row->getRoleNames())) && Auth::user()->can('admin.user.password')) {
                            $actionBtn .= '<a href="' . Route('admin.user.password', $row->id) . '" class="btn btn-sm btn-warning border-0 px-10px fs-15 tt bg-success" data-bs-toggle="tooltip" data-bs-placement="top" title="Change Password"><i class="fal fa-key"></i></a>';
                        }
                    }
                    if ($row->role == 1) {
                        if (!in_array('Software Admin', json_decode($row->getRoleNames())) && Auth::user()->hasRole('Software Admin') || Auth::user()->can('admin.user.destroy') && !in_array('Software Admin', json_decode($row->getRoleNames()))) {
                            $actionBtn .= '<button type="button" class="btn btn-sm btn-danger border-0 px-10px fs-15 tt ' . (!empty($type) && $type == 'trash' ? 'trash_delete' : 'link-delete') . '" data-url="' . Route('admin.user.destroy', $row->id) . '" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete"><i class="far fa-trash-alt"></i></button>';
                        }
                    }
                    $actionBtn .= '</div>';
                    return $actionBtn;
                })
                ->rawColumns(['checkbox', 'image', 'status', 'actions'])
                ->make(true);
        }
        $title = 'User Management';
        return view('admin.user.index', compact('title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title = 'User Informations';
        $roles = Role::whereNotIn('name', ['Software Admin'])->orderBy('name', 'asc')->get();
        return view('admin.user.create', compact('title', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'user_name' => ['required', 'string', 'unique:users,user_name'],
            'email' => ['email', 'nullable', 'unique:users,email'],
            'phone' => ['nullable', 'unique:users,phone'],
            'password' => ['required', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols(), 'confirmed'],
        ]);

        $user = User::create([
            'company_id' => Auth::user()->company_id ?? 1,
            'role' => 1,
            'name' => $request->name,
            'user_name' => $request->user_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'created_by' => Auth::user()->id,
        ]);

        $role = Role::findById($request->role_id);
        $user->assignRole($role);
        return redirect()->Route('admin.user.index')->withSuccessMessage('Created Successfully!');
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
            $data = User::findOrFail($id);
            $data->update(['status' => !$data->status]);
            return response()->json(['status' => 'success']);
        }

        $title = 'User Informations';
        $roles = Role::whereNotIn('name', ['Software Admin'])->orderBy('name', 'asc')->get();
        $data = User::findOrFail($id);
        $link = Route('admin.user.update', $data->id);
        return view('admin.user.edit', compact('title', 'roles', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'user_name' => ['required', 'string', 'unique:users,user_name,' . $id],
            'email' => ['email', 'nullable', 'unique:users,email,' . $id],
            'phone' => ['nullable', 'unique:users,phone,' . $id],
        ]);

        $role = Role::findById($request->role_id);
        $data = User::findOrFail($id);

        $data->update([
            'company_id' => Auth::user()->company_id ?? 1,
            'name' => $request->name,
            'user_name' => $request->user_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'updated_by' => Auth::user()->id,
        ]);
        $data->syncRoles($role);
        return redirect()->route('admin.user.index')->withSuccessMessage('Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return HelperClass::resourceDataDelete(User::class, $id, 'image');
    }

    public function changePassword(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.user.password', compact('user'));
    }

    public function passwordUpdate(Request $request, string $id)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::findOrFail($id);
        $user->password = Hash::make($request->password);
        $user->save();
        return redirect()->route('admin.user.index')->withSuccessMessage('Password Updated Successfully!');
    }
}
