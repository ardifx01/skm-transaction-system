@php
    // Helper format tanggal
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

    // Helper format angka
    function formatNumber($value)
    {
        return isset($value) ? number_format($value, 2, ',', '.') : '-';
    }
@endphp

<h2 style="text-align:center; margin-bottom:15px;">POLIS VIDEI</h2>

<table class="polis-table">
    <tr>
        <td colspan="2">THE ASSURED :</td>
        <td colspan="2"></td>
        <td colspan="2">CERTIFICATE NO</td>
        <td colspan="2">{{ $polisVideData['certificate_no'] ?? '-' }}</td>
    </tr>

    <tr>
        <td colspan="4">{{ $polisVideData['consignee'] ?? '-' }}</td>
        <td colspan="2">DATE OF ISSUE</td>
        <td colspan="2">{{ formatDate($polisVideData['date_of_issue'] ?? null) }}</td>
    </tr>

    <tr>
        <td colspan="4">{{ $polisVideData['consignee_address'] ?? '-' }}</td>
        <td colspan="4"></td>
    </tr>

    <tr>
        <td colspan="8" class="space-row"></td>
    </tr>

    <tr>
        <td colspan="2">FROM :</td>
        <td colspan="2">TO :</td>
        <td colspan="2">TRANSHIPMENT AT :</td>
        <td colspan="2">{{ $polisVideData['transhipment_at'] ?? '-' }}</td>
    </tr>

    <tr>
        <td colspan="2">{{ $polisVideData['from'] ?? '-' }}</td>
        <td colspan="2">{{ $polisVideData['to'] ?? '-' }}</td>
        <td colspan="4"></td>
    </tr>

    <tr>
        <td colspan="8" class="space-row"></td>
    </tr>

    <tr>
        <td colspan="2">SHIP OR VESSEL :</td>
        <td colspan="2">VESSEL REG :</td>
        <td colspan="2">SAILING AND OR ABOUT :</td>
        <td colspan="2">{{ formatDate($polisVideData['sailing_date'] ?? null) }}</td>
    </tr>

    <tr>
        <td colspan="2">{{ $polisVideData['shipping_carrier'] ?? '-' }}</td>
        <td colspan="2">{{ $polisVideData['vessel_reg'] ?? '-' }}</td>
        <td colspan="4"></td>
    </tr>

    <tr>
        <td colspan="8" class="space-row"></td>
    </tr>

    <tr>
        <td colspan="2">VALUE AT :</td>
        <td colspan="2">INVOICE NO :</td>
        <td colspan="2">CONSIGNEE :</td>
        <td colspan="2">{{ $polisVideData['consignee'] ?? '-' }}</td>
    </tr>

    <tr>
        <td colspan="2">{{ $polisVideData['currency'] ?? '-' }}</td>
        <td colspan="2">{{ formatNumber($polisVideData['insured_value'] ?? null) }}</td>
        <td colspan="2"></td>
        <td colspan="2">{{ $polisVideData['consignee_address'] ?? '-' }}</td>
    </tr>

    <tr>
        <td colspan="8" class="space-row"></td>
    </tr>

    <tr>
        <td colspan="8">INTEREST INSURED :</td>
    </tr>

    <tr>
        <td colspan="8">{{ $polisVideData['interest_insured'] ?? '-' }}</td>
    </tr>
</table>
