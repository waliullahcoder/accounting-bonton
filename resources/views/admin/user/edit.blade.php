@extends('layouts.admin.edit_app')

@section('content')
    <div class="row g-3">
        <div class="col-lg-4 col-sm-6">
            <label for="name" class="form-label"><b>Name <span class="text-danger">*</span></b></label>
            <input type="text" placeholder="Name" class="form-control" id="name" name="name" required
                value="{{ $data->name }}" minlength="3">
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="user_name" class="form-label"><b>User ID <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control" id="user_name" name="user_name" placeholder="User ID"
                required value="{{ $data->user_name }}">
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="email" class="form-label"><b>Email</b></label>
            <input type="email" class="form-control" id="email" name="email" placeholder="User Email"
                value="{{ $data->email }}">
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="phone" class="form-label"><b>Phone</b></label>
            <input type="text" class="form-control" id="phone" name="phone" placeholder="User Phone"
                value="{{ $data->phone }}">
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="role_id" class="form-label require"><b>Role <span class="text-danger">*</span></b></label>
            <select class="form-control select" name="role_id" id="role_id" required>
                @foreach ($roles as $role)
                    @if (!Auth::user()->hasRole('Software Admin') && $role->name == 'System Admin')
                        @continue
                    @endif
                    <option value="{{ $role->id }}" {{ $data->hasRole($role->name) ? 'selected' : '' }}>
                        {{ $role->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
@endsection
