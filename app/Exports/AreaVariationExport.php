<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class AreaVariationExport implements FromArray, WithHeadings, WithCustomStartCell, WithStyles, WithTitle
{
    protected $data;
    protected $ward;
    protected $zone;
    protected $roadname;

    public function __construct(array $data, $roadname)
    {
        $this->data = $data;
        $this->roadname = $roadname ?? 'N/A';

        // Use collect() to avoid errors when accessing first element
        $firstItem = collect($data)->first();

        $this->ward = $firstItem->ward ?? 'Unknown Ward';
        $this->zone = $firstItem->zone ?? 'Unknown Zone';
    }

    /**
     * Specify the start cell for the export.
     */
    public function startCell(): string
    {
        return 'A1';
    }

    /**
     * Prepare the array of data to be exported.
     */
    public function array(): array
    {
        $exportData = [];
        foreach ($this->data as $index => $point) {
            $exportData[] = [
                'S.NO' => $index + 1,
                'GIS ID' => $point->point_gisid ?? 'N/A',
                'Road Name' => $point->road_name ?? 'N/A',
                'Assessment' => $point->assessment ?? 'N/A',
                'Old Assessment' => $point->old_assessment ?? 'N/A',
                'Owner Name' => $point->owner_name ?? 'N/A',
                'Phone Number' => $point->phone_number ?? 'N/A',
                'New Door No' => $point->new_door_no ?? $point->old_door_no ?? 'N/A',
                'Building Usage' => $point->misusage ?? 'N/A',
                'Bill Usage' => $point->bill_usage ?? 'N/A',
                'Plot Area' => $point->plot_area ?? 'N/A',
                'Basement' => $point->basement ?? 'N/A',
                'Floor' => $point->number_floor ?? 'N/A',
                'Percentage' => $point->percentage ?? 'N/A',
                'Total Drone Area' => $point->totaldronearea ?? 'N/A',
                'Area Variation' => $point->areavariation ?? 'N/A',
                'Half-Year Tax' => $point->halfyeartax ?? 'N/A',
                'Balance' => $point->balance ?? 'N/A',
            ];
        }
        return $exportData;
    }

    /**
     * Specify the column headings for the export.
     */
    public function headings(): array
    {
        return [
            ["Zone: $this->zone - Ward: $this->ward"],  // Ensuring correct display of zone and ward
            ["$this->roadname - AREA VARIATION"],  // Correct display of road name
            [
                'S.NO',
                'GIS ID',
                'Road Name',
                'Assessment',
                'Old Assessment',
                'Owner Name',
                'Phone Number',
                'New Door No',
                'Building Usage',
                'Bill Usage',
                'Plot Area',
                'Basement',
                'Floor',
                'Percentage',
                'Total Drone Area',
                'Area Variation',
                'Half-Year Tax',
                'Balance',
            ],
        ];
    }

    /**
     * Apply styles to the worksheet.
     */
    public function styles(Worksheet $sheet)
    {
        // Merge headers
        $sheet->mergeCells('A1:R1');
        $sheet->mergeCells('A2:R2');

        // Style for Zone & Ward row
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF9BC2E6'],
            ],
        ]);

        // Style for Road Name - Area Variation
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['argb' => 'FF000000']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFB6D7A8'],
            ],
        ]);

        // Style for column headings
        $sheet->getStyle('A3:R3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['argb' => 'FFFFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF404040'],
            ],
        ]);

        // Auto-size columns for better readability
        foreach (range('A', 'R') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
    }

    /**
     * Define the title of the worksheet.
     */
    public function title(): string
    {
        return 'Area Variation';
    }
}
