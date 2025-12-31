<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;

class multisheetExport implements WithMultipleSheets
{
    private $id;
    public function __construct($id)
    {
        $this->id = $id;
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new VotingReportExport($this->id);
        $sheets[] = new VotingMemberList($this->id);
        return $sheets;
    }
}
