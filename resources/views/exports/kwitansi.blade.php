<!DOCTYPE html>
<html>

<head>
    <title>KWITANSI</title>
    <meta charset="UTF-8">
</head>

<body>
    <table border="0" cellspacing="0" cellpadding="5" width="100%">
        <!-- Nama penerima -->
        <tr>
            <td colspan="6"><strong>Bpk. {{ $kwitansiData['user_name'] ?? '-' }}</strong></td>
        </tr>

        <!-- Spacer -->
        <tr>
            <td colspan="6" height="10"></td>
        </tr>

        <!-- Uang dalam kata -->
        <tr>
            <td colspan="6">{{ $kwitansiData['premium_price_in_words'] ?? '-' }}</td>
        </tr>

        <!-- No Polis -->
        <tr>
            <td colspan="6">MARINE CARGO POLICY NO : {{ $kwitansiData['no_policy'] ?? '-' }}</td>
        </tr>

        <!-- Spacer -->
        <tr>
            <td colspan="6" height="20"></td>
        </tr>

        <!-- Tanggal -->
        <tr>
            <td colspan="4"></td>
            <td>Jakarta</td>
            <td>{{ now()->format('d-M-Y') }}</td>
        </tr>

        <!-- Spacer -->
        <tr>
            <td colspan="6" height="20"></td>
        </tr>

        <!-- Nominal -->
        <tr>
            <td><strong>IDR</strong></td>
            <td colspan="5"><strong>{{ number_format($kwitansiData['premium_price'] ?? 0, 2, ',', '.') }}</strong>
            </td>
        </tr>
    </table>
</body>

</html>
