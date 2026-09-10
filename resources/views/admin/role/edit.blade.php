@extends('layouts.admin.edit_app')

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <label for="name" class="form-label require"><b>Name <span class="text-danger">*</span></b></label>
            <input type="text" placeholder="Name" class="form-control" id="name" name="name" required
                value="{{ $data->name }}" minlength="3">
        </div>
    </div>
@endsection
