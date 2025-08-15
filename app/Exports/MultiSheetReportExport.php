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
            new PolisVideSheet($this->data['polis_vide']),
            new LaporanVideSheet($this->data['laporan_vide']),
            new KwitansiSheet($this->data['kwitansi']),
        ];

        return $sheets;
    }
}