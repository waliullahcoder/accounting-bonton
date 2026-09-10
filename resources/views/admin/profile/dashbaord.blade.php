@extends('layouts.admin.app')

@section('content')
@endsection

@push('js')
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.min.js"></script>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    {{-- <script type="text/javascript">
        $(document).ready(function() {
            $.ajax({
                type: 'get',
                url: "{{ route('admin.dashboard') }}",
                data: {},
                success: function(response) {
                    if (response.status == 'success') {
                        var barData = {
                            labels: response.days,
                            datasets: [{
                                label: "Sales",
                                backgroundColor: '#2ecc71',
                                data: response.sales
                            }, ]
                        };
                        var barOptions = {
                            responsive: true,
                            maintainAspectRatio: false
                        };
                        var ctx = document.getElementById("bar_chart").getContext("2d");
                        new Chart(ctx, {
                            type: 'bar',
                            data: barData,
                            options: barOptions
                        });
                    }
                }
            });

            $(document).on('change', '#month', function(e) {
                $('#changed_form')[0].submit();
            });
        });
    </script> --}}
@endpush
