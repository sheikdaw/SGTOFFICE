<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MultiStreetExport implements WithMultipleSheets
{
    protected $mis;

    public function __construct($mis)
    {
        $this->mis = $mis;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->mis as $roadName => $data) {
            $sheets[] = new StreetExport($roadName, $data);
        }

        return $sheets;
    }
}
