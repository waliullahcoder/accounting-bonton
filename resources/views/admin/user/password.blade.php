@extends('layouts.admin.app')

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <form action="{{ Route('admin.user.password-update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-header pe-2 py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="h6 mb-0 text-uppercase">Change Password</h6>
                            <a href="{{ Route('admin.user.index') }}" class="btn btn-primary btn-sm">Go
                                Back</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="password" class="form-label require"><b>Password <span
                                            class="text-danger">*</span></b></label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Password" required>
                            </div>

                            <div class="col-12">
                                <label for="confirm_password" class="form-label require"><b>Password <span
                                            class="text-danger">*</span></b></label>
                                <input type="password" class="form-control" id="confirm_password"
                                    name="password_confirmation" placeholder="Confirm Password" required>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end pe-2 py-2">
                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
