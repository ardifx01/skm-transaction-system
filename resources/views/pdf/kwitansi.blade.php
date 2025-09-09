<table>
    <tr>
        <td colspan="6" class="bold">
            Bpk. {{ $kwitansiData['user_name'] ?? '-' }}
        </td>
    </tr>
    <tr>
        <td colspan="6" class="row-space">
            {{ $kwitansiData['premium_price_in_words'] ?? '-' }}
        </td>
    </tr>
    <tr>
        <td colspan="6">
            MARINE CARGO POLICY NO : {{ $kwitansiData['no_policy'] ?? '-' }}
        </td>
    </tr>
    <tr>
        <td colspan="6" class="row-space"></td>
    </tr>
    <tr>
        <td colspan="4"></td>
        <td>Jakarta</td>
        <td>{{ now()->format('d-m-Y') }}</td>
    </tr>
    <tr>
        <td colspan="6" class="row-space"></td>
    </tr>
    <tr>
        <td class="bold">IDR</td>
        <td colspan="5" class="bold">
            {{ number_format($kwitansiData['premium_price'] ?? 0, 2, ',', '.') }}
        </td>
    </tr>
</table>
