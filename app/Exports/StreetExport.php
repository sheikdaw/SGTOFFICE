<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StreetExport implements FromView
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
}
