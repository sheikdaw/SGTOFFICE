<?php

namespace App\Imports;

use App\Models\Mis;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MisImport implements ToModel, WithHeadingRow
{
    protected $tableName;

    public function __construct($tableName)
    {
        $this->tableName = $tableName;
    }

    public function model(array $row)
    {
        try {
            $mis = new Mis();
            $mis->setTable($this->tableName);

            $mis->fill([
                'assessment' => $row['assessment'] ?? null,
                'old_assessment' => $row['old_assessment'] ?? null,
                'number_floor' => $row['number_floor'] ?? null,
                'new_address' => $row['new_address'] ?? null,
                'building_usage' => $row['building_usage'] ?? null,
                'construction_type' => $row['construction_type'] ?? null,
                'road_name' => $row['road_name'] ?? null,
                'phone' => $row['phone'] ?? null,
                'building_type' => $row['building_type'] ?? null,
                'ward' => $row['ward'] ?? null,
                'owner_name' => $row['owner_name'] ?? null,
                'old_door_no' => $row['old_door_no'] ?? null,
                'new_door_no' => $row['new_door_no'] ?? null,
                'plot_area' => $row['plot_area'] ?? null,
                'watertax' => $row['watertax'] ?? null,
                'halfyeartax' => $row['halfyeartax'] ?? null,
                'balance' => $row['balance'] ?? null,
            ]);

            $mis->save();
        } catch (\Exception $e) {
            Log::error('Row Import Error: ' . $e->getMessage());
        }
    }
}
