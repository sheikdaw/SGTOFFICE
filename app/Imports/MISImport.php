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
        return (new Mis)->setTable($this->tableName)->fill([
            'assessment' => $row['assessment'],
            'old_assessment' => $row['old_assessment'],
            'number_floor' => $row['number_floor'],
            'new_address' => $row['new_address'],
            'building_usage' => $row['building_usage'],
            'construction_type' => $row['construction_type'],
            'road_name' => $row['road_name'],
            'phone' => $row['phone'],
            'building_type' => $row['building_type'],
            'ward' => $row['ward'],
            'owner_name' => $row['owner_name'],
            'old_door_no' => $row['old_door_no'],
            'new_door_no' => $row['new_door_no'],
            'plot_area' => $row['plot_area'],
            'watertax' => $row['watertax'],
            'halfyeartax' => $row['halfyeartax'],
            'balance' => $row['balance'],
        ]);
    }
}
