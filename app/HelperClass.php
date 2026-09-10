<?php

namespace App;

use App\Models\AdminMenuAction;
use App\Services\ActionButtons\ActionButtons;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Intervention\Image\Facades\Image;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class HelperClass
{
    public static function resourceDataView($model, string|NULL $image_column, array|NULL $addition_btns, string $route_path, string|NULL $title, string|array|NULL $relation_data = NULL, string|NULL $sum = NULL, $edit = 'yes')
    {
        if (request()->ajax()) {
            $type = request('type');
            if (!empty($type) && $type == 'trash') {
                $model->onlyTrashed();
            }
            $datatables_eloquent = DataTables::eloquent($model);
            $datatables_eloquent->addIndexColumn();
            $datatables_eloquent->addColumn('checkbox', function ($row) use ($relation_data) {
                $count = 0;
                if (is_array($relation_data)) {
                    foreach ($relation_data as $item) {
                        if (count($row->$item) > 0) {
                            $count = 1;
                        }
                    }
                } elseif (!is_null($relation_data) && count($row->$relation_data) > 0) {
                    $count = 1;
                }

                if ($count == 0) {
                    $checkbox = '<div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input ' . (!empty(request('type')) && request('type') == "trash" ? 'trash_multi_checkbox' : 'multi_checkbox') . '" id="' . $row->id . '" name="multi_checkbox[]" value="' . $row->id . '"><label for="' . $row->id . '" class="custom-control-label"></label></div>';
                    return $checkbox;
                } else {
                    return '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="20" viewBox="0 0 16 20">
                                <path id="df12b5039313fc3798dfa93cfb504acd" d="M17,9V7A5,5,0,0,0,7,7V9a2.946,2.946,0,0,0-3,3v7a2.946,2.946,0,0,0,3,3H17a2.946,2.946,0,0,0,3-3V12A2.946,2.946,0,0,0,17,9ZM9,7a3,3,0,0,1,6,0V9H9Zm4.1,8.5-.1.1V17a1,1,0,0,1-2,0V15.6a1.487,1.487,0,1,1,2.1-.1Z" transform="translate(-4 -2)" fill="#9d9da6"></path>
                            </svg>';
                }
            });
            $datatables_eloquent->addColumn('status', function ($row) use ($route_path) {
                $menuAction = AdminMenuAction::where('route', "admin.{$route_path}.edit")->first();
                if ($menuAction) {
                    $currentRoutePermission = Permission::findById($menuAction->permission_id);
                    if (Route::has("admin.{$route_path}.edit") && auth()->user()->can($currentRoutePermission->name)) {
                        return '<div class="form-check form-switch"><input class="form-check-input change-status c-pointer" data-url="' . Route("admin.{$route_path}.edit", $row->id) . '" type="checkbox" name="status"' . ($row->status == 1 ? 'checked' : '') . '></div>';
                    }
                }
            });
            if (!is_null($relation_data) && !is_array($relation_data) && !is_null($sum)) {
                $datatables_eloquent->addColumn($relation_data, function ($row) use ($relation_data, $sum) {
                    return $row->$relation_data->sum($sum);
                });
            }
            $datatables_eloquent->addColumn($image_column, function ($row) use ($image_column) {
                return '<img src="' . asset($row[$image_column]) . '" height="50" alt=""/>';
            });
            $datatables_eloquent->addColumn('actions', function ($row) use ($addition_btns, $relation_data, $edit) {
                $type = request('type');
                $data = [
                    'id' => $row->id,
                    'edit' => !is_null($type) && $type == 'trash' ? false : true,
                ];
                $delete = 'yes';
                $count = 0;
                if (is_array($relation_data)) {
                    foreach ($relation_data as $item) {
                        if (count($row->$item) > 0) {
                            $count = 1;
                        }
                    }
                } elseif (!is_null($relation_data) && count($row->$relation_data) > 0) {
                    $count = 1;
                }
                if ($count == 1) {
                    $delete = 'no';
                }
                if ($count == 1 && $edit == 'conditional') {
                    $edit = 'no';
                } else {
                    $edit = 'yes';
                }
                return ActionButtons::actions($data, $addition_btns, $delete, $edit);
            });
            return $datatables_eloquent->rawColumns(['checkbox', $image_column, 'status', 'actions'])->make(true);
        }

        return view("admin.{$route_path}.index", compact('title'));
    }

    public static function resourceDataEdit(string $model, string $id, string $path, string|NULL $title, array|NULL $additionalData = NULL)
    {
        if (request()->ajax() && request()->has('status')) {
            $data = $model::findOrFail($id);
            $data->update(['status' => !$data->status]);
            return response()->json(['status' => 'success']);
        }

        $data = $model::findOrFail($id);
        return view("admin.{$path}.edit", compact('data', 'title', 'additionalData'));
    }

    public static function resourceDataDelete(string $model, string $id, array|string|NULL $old_image = NULL, array|string|NULL $other_images = NULL)
    {
        // Recovery Deleted Data
        if (request()->has('recovery') && request('recovery') == 'true') {
            $data = $model::onlyTrashed()->findOrFail($id);
            $data->restore();
            return response()->json(['status' => 'success']);
        }

        // Delete Multiple Items Permanent
        if (request()->has('id') && request()->has('parmanent') && request('parmanent') == 'true') {
            foreach (request('id') as $id) {
                $data = $model::onlyTrashed()->findOrFail($id);

                if (is_array($old_image)) {
                    foreach ($old_image as $item) {
                        if (file_exists($data->$item)) {
                            unlink($data->$item);
                        }
                    }
                } elseif (!is_null($old_image) && file_exists($data->$old_image)) {
                    unlink($data->$old_image);
                }

                if (is_array($other_images)) {
                    foreach ($other_images as $item) {
                        $images = $data->$item->pluck('image')->toArray();
                        foreach ($images as $item) {
                            if (file_exists($item)) {
                                unlink($item);
                            }
                        }
                    }
                } elseif (!is_null($other_images)) {
                    $images = $data->$other_images->pluck('image')->toArray();
                    foreach ($images as $item) {
                        if (file_exists($item)) {
                            unlink($item);
                        }
                    }
                }

                $data->forceDelete();
            }
            return response()->json(['status' => 'success']);
        }

        // Delete Single Item Permanent
        if (request()->has('parmanent') && request('parmanent') == 'true') {
            $data = $model::onlyTrashed()->findOrFail($id);
            if (is_array($old_image)) {
                foreach ($old_image as $item) {
                    if (file_exists($data->$item)) {
                        unlink($data->$item);
                    }
                }
            } elseif (!is_null($old_image) && file_exists($data->$old_image)) {
                unlink($data->$old_image);
            }

            if (is_array($other_images)) {
                foreach ($other_images as $item) {
                    $images = $data->$item->pluck('image')->toArray();
                    foreach ($images as $item) {
                        if (file_exists($item)) {
                            unlink($item);
                        }
                    }
                }
            } elseif (!is_null($other_images)) {
                $images = $data->$other_images->pluck('image')->toArray();
                foreach ($images as $item) {
                    if (file_exists($item)) {
                        unlink($item);
                    }
                }
            }

            $data->forceDelete();
            return response()->json(['status' => 'success']);
        }

        // Delete Multiple Items
        if (request()->has('id')) {
            foreach (request('id') as $id) {
                $data = $model::findOrFail($id);
                $tableName = $model::getModel()->getTable();
                if (Schema::hasColumn($tableName, 'deleted_by')) {
                    $data->update(['deleted_by' => Auth::user()->id]);
                }
                $data->delete();
            }
            return response()->json(['status' => 'success']);
        }

        // Delete Single Item
        $data = $model::findOrFail($id);
        $tableName = $model::getModel()->getTable();
        if (Schema::hasColumn($tableName, 'deleted_by')) {
            $data->update(['deleted_by' => Auth::user()->id]);
        }
        $data->delete();

        return response()->json(['status' => 'success']);
    }

    public static function convertNumber($num)
    {
        $ones = [
            0 => '',
            1 => 'one',
            2 => 'two',
            3 => 'three',
            4 => 'four',
            5 => 'five',
            6 => 'six',
            7 => 'seven',
            8 => 'eight',
            9 => 'nine',
            10 => 'ten',
            11 => 'eleven',
            12 => 'twelve',
            13 => 'thirteen',
            14 => 'fourteen',
            15 => 'fifteen',
            16 => 'sixteen',
            17 => 'seventeen',
            18 => 'eighteen',
            19 => 'nineteen'
        ];

        $tens = [
            0 => '',
            2 => 'twenty',
            3 => 'thirty',
            4 => 'forty',
            5 => 'fifty',
            6 => 'sixty',
            7 => 'seventy',
            8 => 'eighty',
            9 => 'ninety'
        ];

        $higherUnits = [
            1 => 'thousand',
            2 => 'lakh',
            3 => 'crore'
        ];

        if ($num == 0) {
            return 'zero';
        }

        // Split number into groups of 2 or 3 digits for Indian system (lakhs, crores)
        $groups = [];
        while ($num > 0) {
            if (count($groups) == 1) {
                $groups[] = $num % 100; // Group of two digits for thousands
                $num = (int)($num / 100);
            } else {
                $groups[] = $num % 1000; // Groups of three digits for hundreds, lakhs, crores
                $num = (int)($num / 1000);
            }
        }

        // Convert each group into words
        $words = [];
        foreach ($groups as $index => $group) {
            if ($group == 0) {
                continue;
            }

            $groupWords = '';

            // Handle hundreds place
            if ($group >= 100) {
                $groupWords .= $ones[(int)($group / 100)] . ' hundred ';
                $group %= 100;
            }

            // Handle tens and ones
            if ($group >= 20) {
                $groupWords .= $tens[(int)($group / 10)] . ' ' . $ones[$group % 10];
            } else {
                $groupWords .= $ones[$group];
            }

            // Add the scale (thousand, lakh, crore)
            if ($index > 0) {
                $groupWords .= ' ' . $higherUnits[$index] . ' ';
            }

            $words[] = $groupWords;
        }

        return implode(' ', array_reverse($words));
    }

    public static function storeImage($file, $size, $path, $oldImage = NULL)
    {
        $create_path = public_path($path);
        if (!File::isDirectory($create_path)) {
            File::makeDirectory($create_path, 0777, true, true);
        }
        $file_name = Carbon::now()->toDateString() . '-' . Str::random(10);

        $original_extension = $file->getClientOriginalExtension();
        if ($original_extension == 'svg') {
            $file_name = $file_name . '.' . $original_extension;
            $file->move($path, $file_name);
            $path_file_name = $path . '/' . $file_name;
        } else {
            $ext = 'webp';
            $file_name = $file_name . '.' . $ext;
            $file = Image::make($file);
            $file->resize($size, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            })->stream($ext, 100);
            $path_file_name = $path . '/' . $file_name;
            $file->save($path_file_name);
        }
        if (file_exists($oldImage)) {
            unlink($oldImage);
        }
        return ['status' => 'success', 'path_name' => $path_file_name];
    }

    public static function saveImage($image, $width, $save_path, $old_image = NULL)
    {
        $path_name = NULL;
        if (isset($image)) {
            $response = HelperClass::storeImage($image, $width, $save_path, $old_image);
            if ($response['status'] == 'success') {
                $path_name =  $response['path_name'];
            } else {
                $path_name = NULL;
            }
        } else {
            $path_name = NULL;
        }
        return $path_name;
    }
}
