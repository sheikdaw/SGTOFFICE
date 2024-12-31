<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;

class missingBillExport implements FromCollection
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        // Assuming `assessment` and `pointdata` are columns in the respective tables
        return DB::table($this->data->mis)
            ->whereNotIn('assessment', function ($query) {
                $query->select('assessment')
                    ->from($this->data->pointdata);
            })
            ->get();
    }
}
