<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ $report_title }}</title>
    <meta charset="UTF-8">
    <meta name=description content="">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="{{ asset('backend/css/old.bootstrap.min.css') }}" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=PT+Serif:ital,wght@0,400;0,700;1,400;1,700&display=swap"
        rel="stylesheet">
    <style>
        body {
            -webkit-print-color-adjust: exact;
        }

        .heading-area {
            font-family: 'PT Serif', serif;
            font-style: italic;
            text-align: center;
        }

        .heading {
            margin-bottom: 10px;
        }

        .heading-title {
            margin-bottom: 0px;
            line-height: 1.3;
        }

        .heading-info,
        .heading-title {
            font-weight: bold;
        }

        .heading-info {
            font-size: 14px;
            margin-top: 2px;
            font-style: normal;
        }

        @page {
            margin: 0px;
        }

        body {
            margin: 0px;
            padding: 0 20px;
            font-size: 13px;
            font-family: 'PT Serif', serif;
        }

        @media screen,
        print {
            body {
                -webkit-print-color-adjust: exact;
                padding-bottom: 170px;
            }

            .report-title {
                text-align: center;
                font-weight: bold;
                padding: 6px 0;
                background-color: #ddd;
                margin-top: 0;
                -webkit-print-color-adjust: exact;
            }

            .info-table td {
                padding: 2px;
                font-size: 13px;
            }

            .align-middle * {
                vertical-align: middle !important;
            }

            .mb-0 {
                margin-bottom: 0 !important;
            }

            .mb-2 {
                margin-bottom: 0.5rem !important;
            }

            .mb-3 {
                margin-bottom: 1rem !important;
            }

            .signature-item * {
                display: inline-block;
            }

            .signature-item {
                width: 150px;
                border-top: 1px solid #333;
                text-align: center;
                margin: 0 auto;
                position: relative;
            }

            tfoot {
                background-color: #fff;
            }

            .print-footer {
                padding-top: 30px;
                text-align: center;
                padding-bottom: 30px;
                position: fixed;
                left: 0;
                bottom: 0;
                width: 100%;
                z-index: 99;
                background-color: #fff;
                display: block;
            }

            .hotline {
                margin-bottom: 10px;
            }

            .staff {
                position: absolute;
                top: -32px;
                left: 50%;
                transform: translateX(-50%);
                white-space: nowrap;
            }

            thead th {
                font-family: 'PT Serif', serif;
                padding: 0px 12px 8px !important;
            }
        }
    </style>
    @stack('css')
</head>

<body>
    <header class="print-header">
        <table class="table align-middle mb-0">
            <tr>
                <td>
                    <div class="logo-area">
                        <img src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(!is_null($logo) && file_exists($logo) ? public_path($logo) : public_path('backend/images/logo/logo.jpg'))) }}"
                            height="80" alt="Logo" />
                    </div>
                </td>
                <td>
                    <div class="heading-area">
                        <div class="heading text-right mb-3">
                            <h3 class="heading-title">{{ $title }}</h3>
                            <h4 class="heading-info">{!! $informations !!}</h4>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <h4 class="report-title mb-2" style="background-color: #ddd;">{{ $report_title }}</h4>
    </header>
    @yield('content')
    <script type="text/javascript">
        window.print();
    </script>
</body>

</html>
