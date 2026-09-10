@extends('layouts.admin.create_app')

@section('content')
    <div class="row g-3">
        <div class="col-lg-4 col-sm-6">
            <label for="role_id" class="form-label"><b>Role <span class="text-danger">*</span></b></label>
            <select class="form-select select" name="role_id" id="role_id" data-placeholder="Select Role" required>
                @foreach ($roles as $role)
                    @if (!Auth::user()->hasRole('Software Admin') && $role->name == 'System Admin')
                        @continue
                    @endif
                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="hide_on_client col-lg-4 col-sm-6">
            <label for="name" class="form-label"><b>Name <span class="text-danger">*</span></b></label>
            <input type="text" placeholder="Name" class="form-control" id="name" name="name" required
                value="{{ old('name') }}" minlength="3">
        </div>
        <div class="hide_on_client col-lg-4 col-sm-6">
            <label for="user_name" class="form-label"><b>User ID <span class="text-danger">*</span></b></label>
            <input type="text" class="form-control" id="user_name" name="user_name" placeholder="User ID" required
                value="{{ old('user_name') }}">
        </div>
        <div class="hide_on_client col-lg-4 col-sm-6">
            <label for="email" class="form-label"><b>Email</b></label>
            <input type="email" class="form-control" id="email" name="email" placeholder="User Email"
                value="{{ old('email') }}">
        </div>
        <div class="hide_on_client col-lg-4 col-sm-6">
            <label for="phone" class="form-label"><b>Phone</b></label>
            <input type="text" class="form-control" id="phone" name="phone" placeholder="User Phone"
                value="{{ old('phone') }}">
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="password" class="form-label"><b>Password <span class="text-danger">*</span></b></label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required
                value="{{ old('password') }}">
        </div>
        <div class="col-lg-4 col-sm-6">
            <label for="confirm_password" class="form-label"><b>Password <span class="text-danger">*</span></b></label>
            <input type="password" class="form-control" id="confirm_password" name="password_confirmation"
                placeholder="Confirm Password" required>
        </div>
    </div>
@endsection
