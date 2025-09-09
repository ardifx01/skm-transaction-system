<?php
namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;

class PDFController extends Controller
{
    public function generatePDF()
    {
        // Ambil isi view dan gabungkan
        $html  = view('pdf.polis')->render();
        $html .= view('pdf.laporan')->render();
        $html .= view('pdf.invoice')->render();

        $pdf = Pdf::loadHTML($html)->setPaper('A4', 'portrait');

        return $pdf->download('dokumen.pdf');
        // atau kalau mau tampil di browser:
        // return $pdf->stream('dokumen.pdf');
    }
}
