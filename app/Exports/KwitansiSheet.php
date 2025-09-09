<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;

class KwitansiSheet implements FromView, WithTitle
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.kwitansi', [
            'kwitansiData' => $this->data,
        ]);
    }

    public function title(): string
    {
        return 'KWITANSI';
    }
}