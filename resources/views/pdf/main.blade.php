<!DOCTYPE html>
<html>

<head>
    <title>Dokumen PDF</title>
    <meta charset="UTF-8">
    <style>
        /* Style CSS utama untuk keseluruhan PDF */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            /* Hapus spasi antar sel */
        }

        td {
            padding: 5px;
            vertical-align: top;
        }

        .page-break {
            page-break-after: always;
        }

        /* Styles spesifik untuk polis.blade.php */
        .polis-table td {
            border: 1px solid #000;
        }

        /* Styles spesifik untuk kwitansi.blade.php */
        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .row-space {
            margin-bottom: 10px;
        }

        /* Styles spesifik untuk laporan.blade.php */
        .header-text {
            margin-bottom: 5px;
        }

        .laporan-table {
            margin-top: 15px;
        }
    </style>
</head>

<body>
    @yield('content')
</body>

</html>
