<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MissingBillExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return DB::table($this->data->mis)
            ->select([
                'assessment',
                'old_assessment',
                'owner_name',
                'old_door_no',
                'phone',
                'road_name',
                'building_usage',

            ])
            ->whereNotIn('assessment', function ($query) {
                $query->select('assessment')
                    ->from($this->data->pointdata);
            })
            ->get();
    }

    public function headings(): array
    {
        return [
            'Assessment',
            'Old Assessment',
            'Owner Name',
            'Old Door No',
            'Phone',
            'Road Name',
            'Building Usage',

        ];
    }
}
