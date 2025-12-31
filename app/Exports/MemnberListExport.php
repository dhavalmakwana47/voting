<?php

namespace App\Exports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MemnberListExport implements FromCollection, WithHeadings
{
    protected $id, $fields;

    // Pass the selected fields through the constructor
    public function __construct($id, $fields)
    {
        $this->id = $id;
        $this->fields = $fields;
    }

    // Fetch only the selected fields
    public function collection()
    {
        return Member::where('resolution_id', $this->id)
            ->get($this->fields);
    }

    // Return dynamic headings based on the selected fields
    public function headings(): array
    {
        // Map field names to human-readable labels
        $fieldHeadings = [
            'name' => 'Member Name',
            'share' => 'Share',
            'email' => 'Email',
            'user_name' => 'Username',
            'password' => 'Password',
            'phone' => 'Phone',
            'email_sent' => 'Email Status',
            'reason' => 'Reason',
            'sent_date' => 'Sent Date',
            'delivery_date' => 'Delivery Date'
        ];

        // Return the headings for the selected fields
        return array_map(function ($field) use ($fieldHeadings) {
            return $fieldHeadings[$field];
        }, $this->fields);
    }
}
