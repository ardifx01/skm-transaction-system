<?php 
namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiSheetReportExport implements WithMultipleSheets
{
    use Exportable;

    protected $data; 

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        $sheets = [
            new PolisVideSheet($this->data['polis_videi']),
            new LaporanVideSheet($this->data['laporan_videi_data'], $this->data['laporan_videi_report_data']),
            new KwitansiSheet($this->data['kwitansi']),
        ];

        return $sheets;
    }
}