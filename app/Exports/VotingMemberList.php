<?php

namespace App\Exports;

use App\Models\Resolution;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Illuminate\Contracts\View\View;

class VotingMemberList implements FromView, WithStrictNullComparison
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
        $data['resolution'] = $resolution;
        if ($resolution->evsn_type != '2') {
        return view('app.votingreport.memberlist', $data);
        }else{
            return view('app.votingreport.option-memberlist', $data);
        }
    }
}
