<?php

namespace App\Http\Controllers;

use App\Mail\VoterEmail;
use App\Models\Member;
use App\Models\OptinonVoting;
use App\Models\Resolution;
use App\Models\ResolutionDetail;
use App\Models\Vote;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\DataTables;

class VoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [];
        $now = Carbon::now();

        $query = Resolution::with('company')
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where('is_active', 1);

        // Check user type and adjust query accordingly
        if (auth()->user()->type != 0) {
            $query->where('user_id', auth()->user()->id);
        }

        // Get the results first
        $data['resolutionArr'] = $query->get();

        // Now order by company name after fetching the results
        $data['resolutionArr'] = $data['resolutionArr']->sortBy(function ($resolution) {
            return $resolution->company->name; // Ensure the 'company' relation is loaded
        });

        // Optionally, reset the keys if needed
        $data['resolutionArr'] = $data['resolutionArr']->values();

        return view('app.vote.votingstatus', $data);
    }

    public function share_count(Request $request)
    {
        $data = [];
        $resolution = Resolution::find($request->resolution_id);
        $total_share = Member::where('resolution_id', $request->resolution_id)->sum('share');
        $total_member = Member::where('resolution_id', $request->resolution_id)->count();
        if ($resolution->evsn_type == '2') {
            $voted_member =  OptinonVoting::where('resolution_id', $request->resolution_id)->distinct('member_id')->count() ;
            $voted_share = Member::whereIn('id', OptinonVoting::where('resolution_id', $request->resolution_id)->pluck('member_id'))->sum('share');
        } else {
            $voted_member =  Vote::where('resolution_id', $request->resolution_id)->pluck('member_id')->count() / $resolution->resolution_details->count();
            $voted_share = Member::whereIn('id', Vote::where('resolution_id', $request->resolution_id)->pluck('member_id'))->sum('share');
        }
        $votedSharePercentage =  $total_share > 0 ? number_format(($voted_share / $total_share) * 100, 2) : 0;
        $unvotedSharePercentage =  $total_share > 0 ? number_format((($total_share - $voted_share) / $total_share) * 100, 2) : 0;
        $data['total_member'] = $total_member . " (100%)";
        $data['voted_share'] = $voted_member . " (" . $votedSharePercentage . "%)";
        $data['unvoted_share'] = ($total_member - $voted_member) . " (" . $unvotedSharePercentage . "%)";
        return $data;
    }

    public function list(Request $request)
    {
        if ($request->ajax()) {
            $data = Member::with(['vote', 'option_votes'])
                ->where('members.resolution_id', $request->resolution_id);
            
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('vote_status', function ($row) {
                    if ($row->vote || $row->option_votes->count() > 0) {
                        return "Yes";
                    } else {
                        return "No";
                    }
                })
                ->orderColumn('vote_status', function ($query, $order) use  ($request) {
                    $query->orderByRaw('(
                        CASE WHEN EXISTS(
                            SELECT 1 FROM votes WHERE votes.member_id = members.id AND votes.resolution_id = ?
                        ) OR EXISTS(
                            SELECT 1 FROM optinon_votings WHERE optinon_votings.member_id = members.id AND optinon_votings.resolution_id = ?
                        ) THEN 0 ELSE 1 END
                    ) ' . $order, [$request->resolution_id, $request->resolution_id]);
                })
                ->rawColumns(['action'])
                ->escapeColumns([])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     */

    private function generatePDF($data)
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml(view('app.vote.recipt', $data));

        // (Optional) Set paper size and orientation
        $dompdf->setPaper('A3', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        return $dompdf;
    }

    public function voting_recipt(Request $request)
    {
        $data = [];
        $data['member'] = Member::find($request->member_id);
        $resolution = Resolution::find($data['member']->resolution_id);

        $data['resolution'] =  $resolution;
        if ($resolution->evsn_type == '2') {
            $data['votes'] = OptinonVoting::where('member_id', $request->member_id)->get();
        } else {
            $data['votes'] = Vote::where('member_id', $request->member_id)->get();
        }


        // return view('app.vote.recipt', $data);
        $pdf = $this->generatePDF($data);
        $logData['member_id'] = $request->member_id;
        $logData['resolution_id'] = $data['member']->resolution_id;
        $logData['action'] = "'{$data["member"]->name}' has downloaded a receipt.";
        addUserAction($logData);

        // Output the PDF
        return $pdf->stream();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $votes = $request->resolution_choice;
            $member = Member::find($request->member_id);
            $now = Carbon::now();
            $resolution = Resolution::where('id', $member->resolution_id)->where('is_active',1)->Where('start_date', '<=', $now)->Where('end_date', '>=', $now)->first();

            if (isset($resolution)) {
                $index = 0;
                if (isset($request->vote_id)) {
                    $voteArr = $request->vote_id;
                }
                $isNewVote = false;
                foreach ($votes as $id => $vote) {
                    if (ResolutionDetail::find($id)) {
                        $voteDetails = Vote::where('resolution_details_id', $id)->where('member_id', $member->id)->first();
                        if (isset($voteDetails)) {
                            $isNewVote = true;

                            $voteDetails->update([
                                'resolution_id' => $member->resolution_id,
                                'member_id' =>  $member->id,
                                'resolution_details_id' => $id,
                                'resolution_choice' => $vote,
                                'voting_date' => Carbon::now()->format('Y-m-d H:i:s'),
                                'ipaddress' => $_SERVER['REMOTE_ADDR'],
                                'updated_by' => $member->id
                            ]);
                        } else {
                            Vote::create([
                                'resolution_id' => $member->resolution_id,
                                'member_id' =>  $member->id,
                                'resolution_details_id' => $id,
                                'resolution_choice' => $vote,
                                'voting_date' => Carbon::now()->format('Y-m-d H:i:s'),
                                'ipaddress' => $_SERVER['REMOTE_ADDR'],
                                'created_by' => $member->id
                            ]);
                        }
                    }

                    $index++;
                }

                $data = $logData = [];
                $logData['member_id'] = $member->id;
                $logData['resolution_id'] = $member->resolution_id;
                $logData['action'] = $member->name . ($isNewVote ? ' updated their vote' : ' voted') . " (Voting ID: {$member->resolution_id})";
                addUserAction($logData);
                $member = Member::find($member->id);
                $data['member'] = $member;
                $data['resolution'] = $resolution;
                $data['blade'] = 'emails.votingdetails';
                $data['subject'] = 'Congratulations! Your Vote Has Been Successfully Submitted (' . $member->company->name . "-" . $member->resolution_id . ")";
                $data['votes'] = Vote::where('member_id', $member->id)->get();
                Mail::to($member->email)->send(new VoterEmail($data));
                // Mail::to('makawanadhaval418@gmail.com')->send(new VoterEmail($data));
            }

            return redirect()->route('member.voting_list')->with('status', 'Congratulations! Your Vote Has Been Successfully Submitted.');
        } catch (\Exception $e) {
            // Handle the exception and redirect back with an error message
            return redirect()->back()->with('error', 'something went wrong.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Vote $vote)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vote $vote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vote $vote)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vote $vote)
    {
        //
    }
}
