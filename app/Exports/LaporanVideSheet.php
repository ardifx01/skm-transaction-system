<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

class LaporanVideSheet implements FromView, WithTitle
{
    protected $data;
    protected $reportData;

    public function __construct(Collection $data, array $reportData)
    {
        $this->data = $data;
        $this->reportData = $reportData;
    }

    public function view(): View
    {
        return view('exports.laporan', [
            'data' => $this->data,
            'tgl_ambil' => $this->reportData['tgl_ambil'],
            'no_registrasi' => $this->reportData['no_registrasi'],
        ]);
    }

    public function title(): string
    {
        return 'Laporan';
    }
}
