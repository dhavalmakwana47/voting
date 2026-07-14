<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;

class ConvertToArray implements ToCollection, WithHeadingRow, WithMapping
{
    public function collection(Collection $rows)
    {
        return $rows;
    }

    public function map($row): array
    {
        return array_map(function ($value) {
            if (is_float($value)) {
                return rtrim(rtrim(number_format($value, 10, '.', ''), '0'), '.');
            }
            return $value;
        }, is_array($row) ? $row : $row->toArray());
    }
}
