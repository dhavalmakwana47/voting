<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;

class MemberSampleExcel implements FromArray, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function array(): array
    {
        // Your empty array or data source
        return [];
    }
    public function headings(): array
    {
        // Your logic to define column headings
        return ['name', 'share', 'email', 'phone'];
    }
}
