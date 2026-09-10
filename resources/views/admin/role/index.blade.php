@extends('layouts.admin.index_app')

@section('content')
    @php
        $currentRouteName = \Request::route()->getName();
        $link = Route($currentRouteName);
        $delete_link = str_replace('index', 'destroy', $currentRouteName);
    @endphp
    <div class="card-body">
        <table class="dataTable table align-middle" style="width:100%">
            <thead>
                <tr class="text-nowrap">
                    <th width="3"></th>
                    @if (Auth::user()->hasRole('Software Admin'))
                        <th>Company</th>
                    @endif
                    <th>Name</th>
                    <th width="110" class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            @if (Auth::user()->can($delete_link))
                <tfoot>
                    <tr>
                        <th class="text-center" colspan="1">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="selectAll">
                                <label class="custom-control-label" for="selectAll"></label>
                            </div>
                        </th>
                        <th class="text-end" colspan="{{ Auth::user()->hasRole('Software Admin') ? '3' : '2' }}">
                            <button type="button" name="bulk_delete" data-url="{{ Route('admin.role.destroy', '0') }}"
                                id="bulk_delete" class="btn btn btn-xs btn-danger">Delete</button>
                        </th>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('.dataTable').dataTable({
                processing: true,
                serverSide: true,
                scrollX: true,
                ajax: {
                    url: "{{ $link }}",
                    type: "GET",
                },
                columns: [{
                        data: "checkbox",
                        name: "checkbox",
                        orderable: false,
                        searchable: false,
                        width: '3'
                    },
                    @if (Auth::user()->hasRole('Software Admin'))
                        {
                            data: 'company',
                            name: 'company',
                            defaultContent: ''
                        },
                    @endif {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false,
                        className: "text-end",
                    },
                ],
                "fnDrawCallback": function(oSettings) {
                    const tooltips = document.querySelectorAll('.tt');
                    tooltips.forEach(t => {
                        new bootstrap.Tooltip(t);
                    });
                }
            });
        });
    </script>
@endpush
