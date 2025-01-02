<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssessmentDetailsExport implements FromCollection, WithHeadings
{
    protected $pointdata;

    public function __construct($pointdata)
    {
        $this->pointdata = $pointdata;
    }

    /**
     * Return the point data to export.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Return the data passed to the constructor
        return collect($this->pointdata); // Convert the pointdata to a collection if needed
    }

    /**
     * Set the header for the Excel file.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'assessment' // Add more fields as per your data structure
        ];
    }
}
