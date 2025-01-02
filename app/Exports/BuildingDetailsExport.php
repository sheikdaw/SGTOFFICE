<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BuildingDetailsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $buildingDetails;

    public function __construct($buildingDetails)
    {
        $this->buildingDetails = $buildingDetails;
    }

    /**
     * Return the building data to export.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return collect($this->buildingDetails); // Convert the building details into a collection
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
            'GIS ID',
            'Number Bill',
            'Number Shop',
            'Number Floor',
            'New Address',
            'Liftroom',
            'Headroom',
            'Overhead Tank',
            'Percentage',
            'Building Name',
            'Building Usage',
            'Construction Type',
            'Road Name',
            'UGD',
            'Rainwater Harvesting',
            'Parking',
            'Ramp',
            'Hoarding',
            'CCTV',
            'Cell Tower',
            'Solar Panel',
            'Basement',
            'Water Connection',
            'Phone',
            'Building Type',
            'Image',
            'Sqfeet',
            'Merge',
            'Split',
            'Worker Name',
            'Remarks',
            'Corporation Remarks',
            'Created At',
            'Updated At',
        ];
    }

    /**
     * Map the data for each row in the Excel file.
     *
     * @param \stdClass $building
     * @return array
     */
    public function map($building): array
    {
        return [
            $building->id,
            $building->data_id,
            $building->gisid,
            $building->number_bill,
            $building->number_shop,
            $building->number_floor,
            $building->new_address,
            $building->liftroom,
            $building->headroom,
            $building->overhead_tank,
            $building->percentage,
            $building->building_name,
            $building->building_usage,
            $building->construction_type,
            $building->road_name,
            $building->ugd,
            $building->rainwater_harvesting,
            $building->parking,
            $building->ramp,
            $building->hoarding,
            $building->cctv,
            $building->cell_tower,
            $building->solar_panel,
            $building->basement,
            $building->water_connection,
            $building->phone,
            $building->building_type,
            $building->image,
            $building->sqfeet,
            $building->merge,
            $building->split,
            $building->worker_name,
            $building->remarks,
            $building->corporationremarks,
            $building->created_at,
            $building->updated_at,
        ];
    }
}
