<?php

namespace App\Exports;

use App\Models\Resolution;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Illuminate\Contracts\View\View;

class DeliveryReportExport  implements FromView, WithStrictNullComparison
{
    private $id;
    public function __construct($id)
    {
        $this->id = $id;
    }

    public function view(): View
    {
        $data = [];
        $resolution = Resolution::find($this->id);
        $data['members'] = $resolution->members;
        $data['resolution'] = $resolution;
        return view('emails.deliveryreport', $data);
    }
}
