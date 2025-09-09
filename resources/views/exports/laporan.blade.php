<!DOCTYPE html>

<html>

<head>
    <title>Laporan Vide</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            text-align: center;
        }

        .header {
            font-weight: bold;
            font-size: 18px;
            text-align: center;
            margin-bottom: 20px;
        }

        .info {
            margin-top: 10px;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>
    <table border="1px">
        <thead>
            <tr>
                <th colspan="7" class="header">DAFTAR PELAPORAN PEMAKAIAN VIDEI</th>
            </tr>
            <tr>
                <td colspan="7" class="info">Pengambilan : {{ $tgl_ambil }}</td>
            </tr>
            <tr>
                <td colspan="7" class="info">NO REGISTRASI MC {{ $no_registrasi }}</td>
            </tr>

            <!-- Tambah 3 baris kosong untuk jarak -->
            <tr>
                <td colspan="7">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="7">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="7">&nbsp;</td>
            </tr>

            <!-- Header kolom -->
            <tr>
                <th>NO</th>
                <th>NO BLANKO</th>
                <th>NO POLIS</th>
                <th>CONSIGNEE</th>
                <th>NO BL</th>
                <th>ALAT PENGANGKUT</th>
                <th>NILAI PERTANGGUNGAN</th>
            </tr>
        </thead>
        <tbody>
            @php $row_number = 1; @endphp
            @foreach ($data as $item)
                <tr>
                    <td>{{ $row_number++ }}</td>
                    <td>{{ $item['no_blanko'] }}</td>
                    <td>{{ $item['no_polis'] }}</td>
                    <td>{{ $item['consignee'] }}</td>
                    <td>{{ $item['no_bl'] }}</td>
                    <td>{{ $item['alat_pengangkut'] }}</td>
                    <td>IDR {{ number_format($item['nilai_pertanggungan'], 0, ',', '.') }}</td>

                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
