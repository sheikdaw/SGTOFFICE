<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssessmentDetailsExport implements FromCollection, WithHeadings, WithMapping
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
        return collect($this->pointdata); // Ensure the data is converted to a collection
    }

    /**
     * Set the header for the Excel file.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Data ID',
            'Point GIS ID',
            'Worker Name',
            'Assessment',
            'Old Assessment',
            'Owner Name',
            'Present Owner Name',
            'EB',
            'Floor',
            'Bill Usage',
            'Aadhar No',
            'Ration No',
            'Phone Number',
            'Shop Floor',
            'Shop Name',
            'Shop Owner Name',
            'Old Door No',
            'New Door No',
            'Shop Category',
            'Shop Mobile',
            'License',
            'Professional Tax',
            'GST',
            'Number of Employee',
            'Trade Income',
            'Establishment Remarks',
            'Remarks',
            'Plot Area',
            'Water Tax',
            'Half Year Tax',
            'Balance',
            'Building Data ID',
            'QC Area',
            'QC Usage',
            'QC Name',
            'QC Remarks',
            'OTS Area',
            'Created At',
            'Updated At',
        ];
    }

    /**
     * Map the data for each row in the Excel file.
     *
     * @param \stdClass $point
     * @return array
     */
    public function map($point): array
    {
        return [
            $point->id,
            $point->data_id,
            $point->point_gisid,
            $point->worker_name,
            $point->assessment,
            $point->old_assessment,
            $point->owner_name,
            $point->present_owner_name,
            $point->eb,
            $point->floor,
            $point->bill_usage,
            $point->aadhar_no,
            $point->ration_no,
            $point->phone_number,
            $point->shop_floor,
            $point->shop_name,
            $point->shop_owner_name,
            $point->old_door_no,
            $point->new_door_no,
            $point->shop_category,
            $point->shop_mobile,
            $point->license,
            $point->professional_tax,
            $point->gst,
            $point->number_of_employee,
            $point->trade_income,
            $point->establishment_remarks,
            $point->remarks,
            $point->plot_area,
            $point->water_tax,
            $point->halfyeartax,
            $point->balance,
            $point->building_data_id,
            $point->qc_area,
            $point->qc_usage,
            $point->qc_name,
            $point->qc_remarks,
            $point->otsarea,
            $point->created_at,
            $point->updated_at,
        ];
    }
}
