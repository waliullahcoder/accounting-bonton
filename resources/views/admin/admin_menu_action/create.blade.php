@extends('layouts.admin.app')

@section('content')
    <div class="row g-3">
        <div class="col-12">
            <form action="{{ Route('admin.admin-menuAction.store', $id) }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-header pe-2 py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="h6 mb-0 text-uppercase">Add Admin Menu Action</h6>
                            <div>
                                <a href="{{ Route('admin.admin-menuAction.index', $id) }}"
                                    class="btn btn-primary btn-sm">Go Back</a>
                                <button type="submit" class="btn btn-primary btn-sm">Save</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="name" class="form-label require"><b>Action Label</b></label>
                                <input type="text" class="form-control" id="name" name="name"
                                    required value="{{ old('name') }}" placeholder="Action Label" autofocus>
                            </div>
                            <div class="col-12">
                                <label for="route" class="form-label require"><b>Route</b></label>
                                <input type="text" class="form-control" id="route" name="route"
                                    required value="{{ old('route') }}" placeholder="Route">
                            </div>
                            <div class="col-12">
                                <label for="Status" class="form-label"><b>Status</b></label>
                                <div class="custom-select">
                                    <select class="form-control select2 custom-select__element" name="status"
                                        id="status">
                                        <option value="1" selected>Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-end pe-2 py-2">
                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
