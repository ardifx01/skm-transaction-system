<!DOCTYPE html>
<html>

<head>
    <title>Kwitansi</title>
</head>

<body>
    <table>
        <tr>
            <td colspan="4"></td>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td colspan="4">Bpk. {{ $kwitansiData['user_name'] ?? '-' }}</td>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td colspan="4"></td>
        </tr>
        <tr>
            <td colspan="8">{{ $kwitansiData['premium_price_in_words'] ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="8">MARINE CARGO POLICY NO : {{ $kwitansiData['no_policy'] ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="4"></td>
            <td colspan="2">Jakarta</td>
            <td colspan="2">{{ now()->format('d-M-Y') }}</td>
        </tr>
        <tr>
            <td colspan="8"></td>
        </tr>
        <tr>
            <td colspan="2">IDR</td>
            <td colspan="2">{{ number_format($kwitansiData['premium_price'] ?? 0, 2, ',', '.') }}</td>
            <td colspan="4"></td>
        </tr>
    </table>
</body>

</html>
