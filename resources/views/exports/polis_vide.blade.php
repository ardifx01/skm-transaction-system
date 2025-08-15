<!DOCTYPE html>
<html>

<head>
    <title>Export Invoice</title>
</head>

<body>
    @php
        // Helper function untuk format tanggal
        function formatDate($date)
        {
            if (empty($date) || $date == '-') {
                return '-';
            }
            try {
                return \Carbon\Carbon::parse($date)->format('d F Y');
            } catch (\Exception $e) {
                return '-';
            }
        }

        // Helper function untuk format angka
        function formatNumber($value)
        {
            return isset($value) ? number_format($value, 2, ',', '.') : '-';
        }
    @endphp

    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <td colspan="2">THE ASSURED :</td>
            <td colspan="2"></td>
            <td colspan="2">CERTIFICATE NO</td>
            <td colspan="2">{{ $polisData['certificate_no'] ?? '-' }}</td>
        </tr>

        <tr>
            <td colspan="4">{{ $polisData['consignee'] ?? '-' }}</td>
            <td colspan="2">DATE OF ISSUE</td>
            <td colspan="2">{{ formatDate($polisData['date_of_issue'] ?? null) }}</td>
        </tr>

        <tr>
            <td colspan="4">{{ $polisData['consignee_address'] ?? '-' }}</td>
            <td colspan="4"></td>
        </tr>

        <tr>
            <td colspan="8"></td>
        </tr>

        <tr>
            <td colspan="2">FROM :</td>
            <td colspan="2">TO :</td>
            <td colspan="2">TRANSHIPMENT AT :</td>
            <td colspan="2">{{ $polisData['transhipment_at'] ?? '-' }}</td>
        </tr>

        <tr>
            <td colspan="2">{{ $polisData['from'] ?? '-' }}</td>
            <td colspan="2">{{ $polisData['to'] ?? '-' }}</td>
            <td colspan="4"></td>
        </tr>

        <tr>
            <td colspan="8"></td>
        </tr>

        <tr>
            <td colspan="2">SHIP OR VESSEL :</td>
            <td colspan="2">VESSEL REG :</td>
            <td colspan="2">SAILING AND OR ABOUT :</td>
            <td colspan="2">{{ formatDate($polisData['sailing_date'] ?? null) }}</td>
        </tr>

        <tr>
            <td colspan="2">{{ $polisData['shipping_carrier'] ?? '-' }}</td>
            <td colspan="2">{{ $polisData['vessel_reg'] ?? '-' }}</td>
            <td colspan="4"></td>
        </tr>

        <tr>
            <td colspan="8"></td>
        </tr>

        <tr>
            <td colspan="2">VALUE AT :</td>
            <td colspan="2">INVOICE NO :</td>
            <td colspan="2">CONSIGNEE :</td>
            <td colspan="2">{{ $polisData['consignee'] ?? '-' }}</td>
        </tr>

        <tr>
            <td colspan="2">{{ $polisData['currency'] ?? '-' }}</td>
            <td colspan="2">{{ formatNumber($polisData['insured_value'] ?? null) }}</td>
            <td colspan="2"></td>
            <td colspan="2">{{ $polisData['consignee_address'] ?? '-' }}</td>
        </tr>

        <tr>
            <td colspan="8"></td>
        </tr>

        <tr>
            <td colspan="8">INTEREST INSURED :</td>
        </tr>

        <tr>
            <td colspan="8">{{ $polisData['interest_insured'] ?? '-' }}</td>
        </tr>
    </table>
</body>

</html>
