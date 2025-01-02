<?php

namespace App\Exports;



use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PointDataExport implements FromCollection, WithHeadings, WithMapping
{
    protected $pointdata;

    // Constructor to accept the pointdata collection
    public function __construct($pointdata)
    {
        $this->pointdata = $pointdata;
    }

    public function collection()
    {
        return $this->pointdata; // Return the passed collection
    }

    public function headings(): array
    {
        return [
            'ID',
            'Data ID',
            'Point GISID',
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
            'Number of Employees',
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
            'Updated At'
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->data_id,
            $row->point_gisid,
            $row->worker_name,
            $row->assessment,
            $row->old_assessment,
            $row->owner_name,
            $row->present_owner_name,
            $row->eb,
            $row->floor,
            $row->bill_usage,
            $row->aadhar_no,
            $row->ration_no,
            $row->phone_number,
            $row->shop_floor,
            $row->shop_name,
            $row->shop_owner_name,
            $row->old_door_no,
            $row->new_door_no,
            $row->shop_category,
            $row->shop_mobile,
            $row->license,
            $row->professional_tax,
            $row->gst,
            $row->number_of_employee,
            $row->trade_income,
            $row->establishment_remarks,
            $row->remarks,
            $row->plot_area,
            $row->water_tax,
            $row->halfyeartax,
            $row->balance,
            $row->building_data_id,
            $row->qc_area,
            $row->qc_usage,
            $row->qc_name,
            $row->qc_remarks,
            $row->otsarea,
            $row->created_at,
            $row->updated_at
        ];
    }
}
