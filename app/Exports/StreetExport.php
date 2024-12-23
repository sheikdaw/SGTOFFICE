<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StreetExport implements FromView, WithTitle, WithStyles
{
    protected $roadName;
    protected $data;

    public function __construct($roadName, $data)
    {
        $this->roadName = $roadName;
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.streetwise', [
            'roadName' => $this->roadName,
            'data' => $this->data,
        ]);
    }

    public function title(): string
    {
        return substr(preg_replace('/[^A-Za-z0-9 _-]/', '', $this->roadName), 0, 31);
    }

    public function styles(Worksheet $sheet)
    {
        // Set auto width for all columns
        foreach (range('A', $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Optional: Set header style
        return [
            1 => ['font' => ['bold' => true]], // First row bold
        ];
    }
}
