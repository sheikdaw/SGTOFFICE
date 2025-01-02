<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SurveyorsExport implements FromView
{
    protected $surveyors;

    public function __construct($surveyors)
    {
        $this->surveyors = $surveyors;
    }

    public function view(): View
    {
        return view('exports.surveyors', [
            'surveyors' => $this->surveyors
        ]);
    }
}
