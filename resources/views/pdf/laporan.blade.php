<p class="header-text">Tanggal Ambil: {{ $laporanVideReportData['tgl_ambil'] }}</p>
<p class="header-text">No Registrasi: {{ $laporanVideReportData['no_registrasi'] }}</p>

<table border="1" cellspacing="0" cellpadding="4" width="100%" class="laporan-table">
    <thead>
        <tr>
            <th>No Blanko</th>
            <th>No Polis</th>
            <th>Consignee</th>
            <th>No BL</th>
            <th>Alat Pengangkut</th>
            <th>Nilai Pertanggungan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($laporanVideData as $row)
            <tr>
                <td>{{ $row['no_blanko'] }}</td>
                <td>{{ $row['no_polis'] }}</td>
                <td>{{ $row['consignee'] }}</td>
                <td>{{ $row['no_bl'] }}</td>
                <td>{{ $row['alat_pengangkut'] }}</td>
                <td>Rp {{ number_format($row['nilai_pertanggungan'], 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
