<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class UsageAreaVariationExport implements WithMultipleSheets
{
    protected $areavariation;
    protected $usageVariation;
    protected $AbstractExport;
    protected $rname;

    public function __construct($areavariation, array $usageVariation, $rname)
    {
        // $this->AbstractExport = $AbstractExport;
        $this->areavariation = $areavariation;
        $this->usageVariation = $usageVariation;
        $this->rname = $rname; // Correct assignment
    }

    public function sheets(): array
    {
        return [
            new AreaVariationExport($this->areavariation, $this->rname),
            new UsageVariationExport($this->usageVariation, $this->rname),
            // new AbstractExport($this->AbstractExport, $this->rname),
        ];
    }
}
