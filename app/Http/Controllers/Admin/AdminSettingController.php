<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use Illuminate\Http\Request;
use App\Models\AdminSetting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class AdminSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = AdminSetting::first();
        return view('admin.admin_setting.edit', compact('settings'));
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
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'logo' => 'image',
            'favicon' => 'image',
            'title' => 'required',
            'footer_text' => 'required',
        ]);

        $data = AdminSetting::latest('id')->first();
        if (is_null($data)) {
            $data = new AdminSetting();
        }
        $data->title = $request->title;
        $data->footer_text = $request->footer_text;
        $data->primary_color = $request->primary_color;
        $data->secondary_color = $request->secondary_color;
        $data->collection_head = $request->collection_head;
        $data->facebook = $request->facebook;
        $data->twitter = $request->twitter;
        $data->linkedin = $request->linkedin;
        $data->whatsapp = $request->whatsapp;
        $data->google = $request->google;
        $data->logo = isset($request->logo) ? HelperClass::saveImage($request->logo, 300, 'media/admin-setting/', @$data->logo) : @$data->logo;
        $data->favicon = isset($request->favicon) ? HelperClass::saveImage($request->favicon, 150, 'media/admin-setting/', @$data->favicon) : @$data->favicon;
        $data->save();

        $this->cacheClear();
        return redirect()->back()->withSuccessMessage('Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
