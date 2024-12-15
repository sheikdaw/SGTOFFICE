<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class UsageVariationExport implements FromArray, WithHeadings, WithCustomStartCell, WithStyles
{
    protected $data;
    protected $ward;
    protected $zone;
    protected $roadname;

    public function __construct(array $data, $roadname)
    {
        $this->data = $data;
        $this->roadname = $roadname ?? '';
        $this->ward = $data[0]->ward ?? 'Unknown Ward';
        $this->zone = $data[0]->zone ?? 'Unknown Zone';
    }



    public function startCell(): string
    {
        return 'A1';  // Start data from A1
    }

    public function array(): array
    {
        $exportData = [];

        // Add rows for each point
        foreach ($this->data as $index => $point) {
            $exportData[] = [
                'S.NO' => $index + 1,
                'gisid' => $point->point_gisid ?? null, // Use object notation here
                'road_name' => $point->road_name ?? null, // Use object notation here
                'assessment' => $point->assessment ?? null, // Use object notation here
                'old_assessment' => $point->old_assessment ?? null, // Use object notation here
                'building_usage' => $point->building_usage ?? null, // Use object notation here
                'bill_usage' => $point->bill_usage ?? null, // Use object notation here
                'owner_name' => $point->owner_name ?? null, // Use object notation here
                'floor' => $point->floor ?? null, // Use object notation here
                'phone_number' => $point->phone_number ?? null, // Use object notation here
                'plot_area' => $point->plot_area ?? null, // Use object notation here
                'halfyeartax' => $point->halfyeartax ?? null, // Use object notation here
                'balance' => $point->balance ?? null, // Use object notation here
            ];
        }

        return $exportData;
    }


    public function headings(): array
    {
        return [
            ["SOUTH ZONE - WARD 67A"],  // Zone as main title
            ["$this->roadname - USAGE VARIATION"],  // Correct title with the road name
            [   // Table headings
                'S.NO',
                'GIS ID',
                'Road Name',
                'Assessment',
                'Old Assessment',
                'UTS Usage',
                'Surveyed Usage',
                'Owner Name',
                'Floor',
                'Phone Number',
                'UTS Area',
                'Half-Year Tax',
                'Balance',
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge section headers
        $sheet->mergeCells('A1:N1');  // Merge for road name and section header
        $sheet->mergeCells('A2:N2');  // Merge for section title "USAGE VARIATION"

        // Styling for main title
        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['argb' => 'FF000000'], // Black text
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF9BC2E6'], // Light blue background
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Styling for section title (road name + "USAGE VARIATION")
        $sheet->getStyle('A2')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['argb' => 'FF000000'], // Black text
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF9BC2E6'], // Light blue background
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Set column widths for readability
        $sheet->getColumnDimension('A')->setWidth(5);   // S.NO column
        $sheet->getColumnDimension('B')->setWidth(15);  // GIS ID
        $sheet->getColumnDimension('C')->setWidth(25);  // Road Name
        $sheet->getColumnDimension('D')->setWidth(20);  // Assessment
        $sheet->getColumnDimension('E')->setWidth(20);  // Old Assessment
        // Additional columns...
    }

    public function title(): string
    {
        return 'Usage Variation';  // Set custom sheet name
    }
}
