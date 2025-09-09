@extends('pdf.main')

@section('content')
    {{-- Halaman Pertama: Polis VIDE --}}
    @include('pdf.polis', ['polisVideData' => $polisVideData])

    {{-- Halaman Kedua: Laporan VIDE --}}
    <div class="page-break"></div>
    @include('pdf.laporan', [
        'laporanVideReportData' => $laporanVideReportData,
        'laporanVideData' => $laporanVideData,
    ])

    {{-- Halaman Ketiga: Kwitansi --}}
    <div class="page-break"></div>
    @include('pdf.kwitansi', ['kwitansiData' => $kwitansiData])
@endsection
