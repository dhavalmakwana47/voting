<?php

namespace App\Exports;

use App\Models\Member;
use App\Models\Resolution;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class VotingReportExport implements FromView, WithStrictNullComparison

{
    private $id;
    public function __construct($id)
    {
        $this->id = $id;
    }
    public function view(): View
    {
        $data = $detailsArr = [];
        $id = $this->id;
        $resolution = Resolution::find($id);
        $data['resolution'] = $resolution;
        if ($resolution->evsn_type != '2') {
            # code...
            $totalMembers  = $resolution->members;
            foreach ($resolution->resolution_details()->orderBy('index')->get() as $resolutionDetail) {
                $votedMembers  = Member::whereIn('id', $resolutionDetail->votes->pluck('member_id'));
                $absent_no_of_voters = Member::where('resolution_id', $id)->whereNotIn('id',  $resolutionDetail->votes->pluck('member_id'));
                // dd($absent_no_of_voters->sum('share'));

                $assentMembers = Member::whereIn('id', $resolutionDetail->votes->where('resolution_choice', 'YES')->pluck('member_id'));
                $dissentMembers = Member::whereIn('id', $resolutionDetail->votes->where('resolution_choice', 'No')->pluck('member_id'));
                $abstainMembers = Member::whereIn('id', $resolutionDetail->votes->where('resolution_choice', 'ABSTAIN')->pluck('member_id'));

                $total_voting_of_share = $totalMembers->sum('share');
                $total_numbers_of_members = $totalMembers->count();

                $total_voted_of_share = $votedMembers->sum('share');
                $total_numbers_of_voters = $votedMembers->count();
                $total_percentage_of_voted = ($total_voted_of_share / $total_voting_of_share) * 100;

                $assent_voting_of_share =  $assentMembers->sum('share');
                $assent_no_of_voters =  $assentMembers->count();

                if ($resolution->user->user_type != 2) {
                    $assent_percentage =  $total_voted_of_share > 0 ? ($assent_voting_of_share * 100) / $total_voted_of_share : 0;
                } else {
                    $assent_percentage =  $total_voting_of_share > 0 ? ($assent_voting_of_share / $total_voting_of_share) * 100 : 0;
                }
                $dissent_no_of_voters =  $dissentMembers->count();
                $dissent_voting_of_share =  $dissentMembers->sum('share');
                if ($resolution->user->user_type != 2) {
                    $dissent_percentage = $total_voted_of_share > 0 ? ($dissent_voting_of_share * 100) / $total_voted_of_share : 0;
                } else {
                    $dissent_percentage = $total_voting_of_share > 0 ? ($dissent_voting_of_share /  $total_voting_of_share) * 100 : 0;
                }

                $abstain_voting_of_share =  $abstainMembers->sum('share');
                $abstain_no_of_voters =  $abstainMembers->count();
                if ($resolution->user->user_type != 2) {
                    $abstain_percentage = $total_voted_of_share > 0 ? ($abstain_voting_of_share * 100) / $total_voted_of_share : 0;
                } else {
                    $abstain_percentage = $total_voting_of_share > 0 ? ($abstain_voting_of_share / $total_voting_of_share) * 100 : 0;
                }

                $absent_voting_of_share =  $absent_no_of_voters->sum('share');
                $absent_no_of_voters = $absent_no_of_voters->count();
                if ($resolution->user->user_type != 2) {
                    $absent_percentage = $total_voting_of_share > 0 ? ($absent_voting_of_share * 100) / $total_voting_of_share : 0;
                } else {
                    $absent_percentage = $total_voting_of_share > 0 ? ($absent_voting_of_share / $total_voting_of_share) * 100 : 0;
                }



                array_push($detailsArr, [
                    'id' =>  $resolutionDetail->id,
                    'details' =>  $resolutionDetail->description,
                    'assent_no_of_voters' => $assent_no_of_voters,
                    'assent_voting_of_share' => $assent_voting_of_share,
                    'assent_percentage' => $assent_percentage,
                    'dissent_no_of_voters' => $dissent_no_of_voters,
                    'dissent_voting_of_share' => $dissent_voting_of_share,
                    'dissent_percentage' => $dissent_percentage,
                    'abstain_no_of_voters' => $abstain_no_of_voters,
                    'abstain_voting_of_share' => $abstain_voting_of_share,
                    'abstain_percentage' => $abstain_percentage,
                    'absent_no_of_voters' => $absent_no_of_voters,
                    'absent_voting_of_share' => $absent_voting_of_share,
                    'absent_percentage' => $absent_percentage,
                    'total_numbers_of_members' => $total_numbers_of_members,
                    'total_voting_of_share' => $total_voting_of_share,
                    'total_percentage_share' => 100,
                    'total_numbers_of_voters' => $total_numbers_of_voters,
                    'total_voted_of_share' => $total_voted_of_share,
                    'total_percentage_of_voted' => $total_percentage_of_voted,
                ]);
            }
            $data['detailsArr'] = $detailsArr;
            return view('app.votingreport.report', $data);

        }else{
            return view('app.votingreport.option-report', $data);
        }
    }
}
