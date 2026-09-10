@extends('layouts.admin.app')

@push('css')
    <link rel="stylesheet" href="{{ asset('backend/css/jquery.minicolors.css') }}">
@endpush

@section('content')
    <form action="{{ Route('admin.admin-settings.update', '0') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pe-2 py-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <h6 class="h6 mb-0 py-5px">Admin Settings</h6>
                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3 col-sm-6">
                                <label for="title" class="form-label"><b>Title</b></label>
                                <input type="text" id="title" name="title" placeholder="Ttitle"
                                    class="form-control" value="{{ @$settings->title }}" required>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label for="logo" class="form-label"><b>Logo</b></label>
                                <input type="file" id="logo" name="logo" class="form-control" accept="image/*"
                                    {{ file_exists(@$settings->logo) ? '' : 'required' }}>
                                @if (file_exists(@$settings->logo))
                                    <div class="pt-2">
                                        <img src="{{ asset($settings->logo) }}" height="50" alt="Logo">
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label for="favicon" class="form-label"><b>Favicon</b></label>
                                <input type="file" id="favicon" name="favicon" class="form-control" accept="image/*"
                                    {{ file_exists(@$settings->favicon) ? '' : 'required' }}>
                                @if (file_exists(@$settings->favicon))
                                    <div class="pt-2">
                                        <img src="{{ asset($settings->favicon) }}" height="50" alt="Favicon">
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label for="invest_value" class="form-label"><b>Collection Head</b></label>
                                <select name="collection_head" id="collection_head" class="form-select select"
                                    data-placeholder="Select Head" required>
                                    <option value=""></option>
                                    @php
                                        $coas = \App\Models\CoaSetup::where('head_code', 'LIKE', '10103%')->where('transaction', 1)->get();
                                    @endphp
                                    @foreach ($coas as $item)
                                        <option value="{{ $item->id }}"
                                            {{ $settings->collection_head == $item->id ? 'selected' : '' }}>
                                            {{ $item->head_name }} - {{ $item->head_code }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label for="primary_color" class="form-label"><b>Primary Color</b></label>
                                        <input type="text" id="primary_color" name="primary_color"
                                            placeholder="Primary Color" class="form-control color"
                                            value="{{ @$settings->primary_color }}">
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="secondary_color" class="form-label"><b>Secondary Color</b></label>
                                        <input type="text" id="secondary_color" name="secondary_color"
                                            placeholder="Secondary Color" class="form-control color"
                                            value="{{ @$settings->secondary_color }}">
                                    </div>
                                    <div class="col-12">
                                        <label for="footer_text" class="form-label"><b>Footer Text</b></label>
                                        <input type="text" id="footer_text" name="footer_text" placeholder="Footer Text"
                                            class="form-control" value="{{ @$settings->footer_text }}" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label for="facebook" class="form-label"><b>Facebook</b></label>
                                        <input type="text" id="facebook" name="facebook" placeholder="Facebook"
                                            class="form-control" value="{{ @$settings->facebook }}">
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="twitter" class="form-label"><b>Twitter</b></label>
                                        <input type="text" id="twitter" name="twitter" placeholder="Twitter"
                                            class="form-control" value="{{ @$settings->twitter }}">
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="linkedin" class="form-label"><b>Linkedin</b></label>
                                        <input type="text" id="linkedin" name="linkedin" placeholder="Linkedin"
                                            class="form-control" value="{{ @$settings->linkedin }}">
                                    </div>
                                    <div class="col-sm-6">
                                        <label for="whatsapp" class="form-label"><b>Whatsapp</b></label>
                                        <input type="text" id="whatsapp" name="whatsapp" placeholder="Whatsapp"
                                            class="form-control" value="{{ @$settings->whatsapp }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end">
                        <div class="py-1">
                            <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('js')
    <script type="text/javascript" src="{{ asset('backend/js/jquery.minicolors.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            if ($('.color').length) {
                $(".color").each(function() {
                    $(this).minicolors();
                });
            }
        });
    </script>
@endpush
