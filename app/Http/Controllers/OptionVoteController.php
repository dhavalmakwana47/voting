<?php

namespace App\Http\Controllers;

use App\Mail\VoterEmail;
use App\Models\Member;
use App\Models\OptinonVoting;
use App\Models\Resolution;
use App\Models\ResolutionDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OptionVoteController extends Controller
{
    public function store(Request $request)
    {
        try {
            $member = Member::find($request->member_id);
            $votes = $member->option_votes()->count();
            $now = Carbon::now();
            $resolution = Resolution::where('id', $member->resolution_id)->Where('start_date', '<=', $now)->Where('end_date', '>=', $now)->first();
            if (isset($resolution)) {

                if (!$resolution->is_modifiable &&  $votes > 1) {
                    return redirect()->route('member.voting_list')->with('error', 'Your Vote Has Been Already  Submitted.');
                }

                $isNewVote = false;
                $resolution_choice_arr = $request->resolution_choice;
                $commentArr = $request->instr_comment;
                
                // Get all resolution details for this resolution
                $allResolutionDetails = ResolutionDetail::where('resolution_id', $member->resolution_id)->pluck('id')->toArray();
                
                // Get resolution details that are being voted on
                $votedResolutionDetails = $resolution_choice_arr ? array_keys($resolution_choice_arr) : [];
                
                // Delete votes for resolution details that are no longer being voted on
                $toDeleteResolutionDetails = array_diff($allResolutionDetails, $votedResolutionDetails);
                if (!empty($toDeleteResolutionDetails)) {
                    OptinonVoting::where('member_id', $member->id)
                        ->whereIn('resolution_details_id', $toDeleteResolutionDetails)
                        ->delete();
                }
                
                if ($resolution_choice_arr) {
                    foreach ($resolution_choice_arr as $res_detail_id => $option_id) {
                    $resDetails = ResolutionDetail::find($res_detail_id);
                    if ($resDetails->option_type == 'radio') {
                        $vote = $resDetails->option_votes()
                            ->where('member_id', $member->id)
                            ->first();

                        if (isset($vote)) {
                            $vote->update([
                                'option_id' => $option_id,
                                'voting_date' => Carbon::now()->format('Y-m-d H:i:s'),
                                'ipaddress' => $_SERVER['REMOTE_ADDR'],
                                'instr_comment' => isset($commentArr[$res_detail_id]) ? $commentArr[$res_detail_id] : ''
                            ]);
                        } else {
                            $isNewVote = true;

                            OptinonVoting::create([
                                'resolution_id' => $member->resolution_id,
                                'member_id' =>  $member->id,
                                'resolution_details_id' => $res_detail_id,
                                'option_id' => $option_id,
                                'voting_date' => Carbon::now()->format('Y-m-d H:i:s'),
                                'ipaddress' => $_SERVER['REMOTE_ADDR'],
                                'instr_comment' => isset($commentArr[$res_detail_id]) ? $commentArr[$res_detail_id] : ''
                            ]);
                        }
                    } else {
                        $existingVotes = $resDetails->option_votes()
                            ->where('member_id', $member->id)
                            ->pluck('option_id')
                            ->toArray();

                        // Delete votes that are no longer selected
                        $toDelete = array_diff($existingVotes, $option_id);

                        if (!empty($toDelete)) {
                            $resDetails->option_votes()
                                ->where('member_id', $member->id)
                                ->whereIn('option_id', $toDelete)
                                ->delete();
                        }

                        // Now update/create the current votes
                        foreach ($option_id as $singleOptionId) {
                            $existingVote = $resDetails->option_votes()
                                ->where('member_id', $member->id)
                                ->where('option_id', $singleOptionId)
                                ->first();

                            if ($existingVote) {
                                $existingVote->update([
                                    'voting_date' => Carbon::now(),
                                    'ipaddress' => request()->ip(),
                                    'instr_comment' => $commentArr[$res_detail_id] ?? ''
                                ]);
                            } else {
                                OptinonVoting::create([
                                    'resolution_id' => $member->resolution_id,
                                    'member_id' => $member->id,
                                    'resolution_details_id' => $res_detail_id,
                                    'option_id' => $singleOptionId,
                                    'voting_date' => Carbon::now(),
                                    'ipaddress' => request()->ip(),
                                    'instr_comment' => $commentArr[$res_detail_id] ?? ''
                                ]);
                            }
                        }
                    }
                }
                }

                $data = $logData = [];
                $logData['member_id'] = $member->id;
                $logData['resolution_id'] = $member->resolution_id;
                $logData['action'] = !$isNewVote ? 'Update Voting' : 'Voted';
                addUserAction($logData);
                $member = Member::find($member->id);
                $data['member'] = $member;
                $data['blade'] = 'emails.votingdetails';
                $data['subject'] = 'Congratulations! Your Vote Has Been Successfully Submitted (' . $member->company->name . "-" . $member->resolution_id . ")";
                $data['votes'] = OptinonVoting::where('member_id', $member->id)->get();
                $data['resolution'] =  $resolution;

                Mail::to($member->email)->send(new VoterEmail($data));
            }
            return redirect()->route('member.voting_list')->with('status', 'Congratulations! Your Vote Has Been Successfully Submitted.');
        } catch (\Exception $e) {
            Log::error('OptionVoteController@store error: ' . $e->getMessage());
            // Handle the exception and redirect back with an error message
            return redirect()->back()->with('error', 'something went wrong.');
        }
    }
}
