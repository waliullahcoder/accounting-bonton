<?php

namespace App\Http\Controllers\Admin;

use App\HelperClass;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = Setting::first();
        return view('admin.setting.edit', compact('data'));
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
            'favicon' => 'image',
            'logo' => 'image',
            'footer_logo' => 'image',
            'placeholder' => 'image',
        ]);

        $data = Setting::first();
        if (is_null($data)) {
            $data = new Setting();
        }
        $data->title = $request->title;
        $data->primary_mobile = $request->primary_mobile;
        $data->secondary_mobile = $request->secondary_mobile;
        $data->primary_email = $request->primary_email;
        $data->secondary_email = $request->secondary_email;
        $data->office_time = $request->office_time;
        $data->address = $request->address;
        $data->description = $request->description;
        $data->meta_title = $request->meta_title;
        $data->meta_keyword = $request->meta_keyword;
        $data->meta_description = $request->meta_description;
        $data->google_map = $request->google_map;
        $data->facebook_page = $request->facebook_page;
        $data->facebook_group = $request->facebook_group;
        $data->youtube = $request->youtube;
        $data->twitter = $request->twitter;
        $data->linkedin = $request->linkedin;
        $data->google = $request->google;
        $data->whatsapp = $request->whatsapp;
        $data->instagram = $request->instagram;
        $data->pinterest = $request->pinterest;
        $data->meta_image = isset($request->meta_image) ? HelperClass::saveImage($request->meta_image, 800, 'media/default') : @$data->meta_image;
        $data->favicon = isset($request->favicon) ? HelperClass::saveImage($request->favicon, 150, 'media/default') : @$data->favicon;
        $data->logo = isset($request->logo) ? HelperClass::saveImage($request->logo, 300, 'media/default') : @$data->logo;
        $data->footer_logo = isset($request->footer_logo) ? HelperClass::saveImage($request->footer_logo, 300, 'media/default') : @$data->footer_logo;
        $data->placeholder = isset($request->placeholder) ? HelperClass::saveImage($request->placeholder, 300, 'media/default') : @$data->placeholder;
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
