<?php

namespace App\Http\Controllers;

use App\Imports\ConvertToArray;
use App\Mail\VoterEmail;
use App\Models\Company;
use App\Models\Member;
use App\Models\OptinonVotingDetail;
use App\Models\Resolution;
use App\Models\ResolutionDetail;
use App\Models\UserCompanyMap;
use App\Models\Vote;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;

class OptionVotingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $authUser = auth()->user();
        $data = [];
        $data['active'] = '';
        $compayIds = UserCompanyMap::where('user_id', $authUser->id)->pluck('company_id')->toArray();
        $data['companyArr'] = Company::whereIn('id', $compayIds)->get();
        return view('app.resolution.addoptionvoting', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'resolution' => 'required',
            'resolution.*.resolution_files' => 'required|mimes:pdf',
            'resolution.*.description' => 'required',
            'resolution.*.option_type' => 'required',
            'resolution.*.min' => 'required|integer|min:1',
            'resolution.*.max' => 'required|integer|min:1',
            'company' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'member_file' => 'required',
            'evsn_type' => 'required'
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        $member_files = $request->file('member_file');
        $data = Excel::toCollection(new ConvertToArray(), $member_files);
        $data = $data->toArray()[0];
        $destinationPath = 'uploads/members_files';
        $filename = 'member_file_' . time() . '_' . $member_files->getClientOriginalName();
        $member_files->move($destinationPath, $filename);



        $company = Company::find($request->company);
        $replaceChars = [' ', '-', '&', '.', '/']; // Characters you want to replace
        $prefix = substr(str_replace($replaceChars, '_', $company->name), 0, 3);
        $resolution = Resolution::create([
            'user_id' => isset($request->user_id) ? $request->user_id : auth()->user()->id,
            'company_id' => $request->company,
            'evsn_type' => '2',
            'start_date' => Carbon::createFromFormat('d-m-Y g:i:A', $request->start_date)->format('Y-m-d H:i:s'),
            'end_date' => Carbon::createFromFormat('d-m-Y g:i:A', $request->end_date)->format('Y-m-d H:i:s'),
            'member_file' => $filename,
            'is_modifiable' => $request->has('is_modifiable') ? 1 : 0,
            'comment_mode' => $request->has('comment_mode') ? 1 : 0,
            'voting_otp' => $request->has('voting_otp') ? 1 : 0,
        ]);


        $randomCount = 1;
        foreach ($data as $value) {
            Member::create([
                'name' => $value['name'],
                'email' => $value['email'],
                'share' => $value['share'],
                'phone' => $value['phone'],
                'add_by' => auth()->user()->id,
                'user_name' => $prefix . $randomCount . time(),
                'password' => $this->generateRandomPassword(),
                'resolution_id' => $resolution->id,
                'company_id' => $request->company

            ]);
            $randomCount++;
        }

        $index = 0;
        foreach ($request->resolution as  $resolutionData) {
            $resFilename = null;
            if (isset($resolutionData['resolution_files'])) {
                $currentFile = $resolutionData['resolution_files'];
                $destinationPath = 'uploads/resolution_details_files';
                $resFilename = time() . '_' . rand(1000, 9999) . '_' . $currentFile->getClientOriginalName();
                $currentFile->move($destinationPath, $resFilename);
            }
            $description = $resolutionData['description'];
            $optionType = $resolutionData['option_type'];
            $min = $resolutionData['min'];
            $max = $resolutionData['max'];

            $resDetails =  ResolutionDetail::create([
                'resolution_id' => $resolution->id,
                'description' => $description,
                'option_type' => $optionType,
                'min' => $min,
                'max' => $max,
                'skip' => isset($resolutionData['skip']) ? 1 : 0,
                'file_name' => $resFilename,
                'add_by' => auth()->user()->id,
                'index' => $index + 1
            ]);

            foreach ($request->options[$index] as $optionData) {
                $labalFileName = null;
                if (isset($optionData['image'])) {
                    $currentFile = $optionData['image'];
                    $destinationPath = 'uploads/option_files';
                    $labalFileName = time() . '_' . rand(1000, 9999) . '_' . $currentFile->getClientOriginalName();
                    $currentFile->move($destinationPath, $labalFileName);
                }
                OptinonVotingDetail::create([
                    'resolution_id' => $resolution->id,
                    'resolution_details_id' => $resDetails->id,
                    'label' =>  $optionData['name'],
                    'image' => $labalFileName,
                ]);
            }
            $index++;
        }

        $logData = [];
        $logData['user_id'] = auth()->user()->id;
        $logData['resolution_id'] = $resolution->id;
        $logData['action'] = "Option Voting has been created for the company '{$company->name}'.";
        addUserAction($logData);
        return redirect()->route('voting.index')->with('status', 'Resolution added successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {

        $data = [];
        $voting = Resolution::findorfail($id);
        $data['resolution'] = $voting;
        if (auth()->user()->type == "0" ||  ($voting->user_id == auth()->user()->id)) {
            $compayIds = UserCompanyMap::where('user_id', $voting->user_id)->pluck('company_id')->toArray();
            $data['resolutionDetailsArr'] = ResolutionDetail::where('resolution_id', $voting->id)->orderBy('index')->get();
            $data['companyArr'] = Company::whereIn('id', $compayIds)->get();

            if (auth()->user()->type == "0" ||  ($voting->user_id == auth()->user()->id &&  !($voting->is_active))) {
                $data['active'] = '';
            } else {
                $data['active'] = 'disabled';
            }
            return view('app.resolution.addoptionvoting', $data);
        } else {
            return redirect()->route('voting.index');
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        $resolutionId = $id;
        $resolution = Resolution::find($resolutionId);
        $votedMemberIds = $resolution->option_votes->pluck('member_id');
        $resolution->members()->whereNotIn('id', $votedMemberIds)->update(['email_sent' => 'N']);
        if ($resolution->is_active == 1 && auth()->user()->type != '0') {
            $validator = Validator::make($request->all(), [
                'end_date' => 'required|date_format:d-m-Y g:i:A|after:start_date',
            ]);

            $endDate = Carbon::createFromFormat('d-m-Y g:i:A', $request->end_date)->format('Y-m-d H:i:s');

            $validator->validate();
            $resolution->update([
                'end_date' => $endDate
            ]);
            DB::commit(); // ✅ Commit changes

            return redirect()->route('voting.index')->with('status', 'Resolution updated successfully');
        }

        $validator = Validator::make($request->all(), [
            'resolution' => 'required',
            'resolution.*.description' => 'required',
            'resolution.*.option_type' => 'required',
            'start_date' => 'required|date_format:d-m-Y g:i:A|before:end_date',
            'end_date' => 'required|date_format:d-m-Y g:i:A|after:start_date',
        ]);
        try {

            $idArr = array_map(function ($resolution) {
                return isset($resolution['resolution_id']) ? intval($resolution['resolution_id']) : 0;
            }, $request->resolution);
            $option_ids = array_merge(...array_map(function ($optionGroup) {
                return array_map(function ($option) {
                    return isset($option['option_id']) ? intval($option['option_id']) : 0;
                }, $optionGroup);
            }, $request->options));


            $deleteData = ResolutionDetail::where('resolution_id', $resolutionId)->whereNotIn('id', $idArr)->get();



            $validator->validate();

            $company = Company::find($request->company);
            $replaceChars = [' ', '-', '&', '.', '/']; // Characters you want to replace
            $prefix = substr(str_replace($replaceChars, '_', $company->name), 0, 3);

            if ($request->hasFile('member_file')) {
                $member_files = $request->file('member_file');
                $data = Excel::toCollection(new ConvertToArray(), $member_files);
                $data = $data->toArray()[0];
                Member::where('resolution_id', $resolutionId)->delete();

                $randomCount = 1;
                foreach ($data as $value) {
                    Member::create([
                        'name' => $value['name'],
                        'email' => $value['email'],
                        'share' => $value['share'],
                        'phone' => $value['phone'],
                        'add_by' => auth()->user()->id,
                        'user_name' => $prefix . $randomCount . time(),
                        'password' => $this->generateRandomPassword(),
                        'resolution_id' => $resolution->id,
                        'company_id' => $request->company

                    ]);
                    $randomCount++;
                }

                $destinationPath = 'uploads/members_files';
                $filename = 'member_file_' . time() . '_' . $member_files->getClientOriginalName();
                $member_files->move($destinationPath, $filename);
                if (File::exists(public_path('uploads/members_files/' . $resolution->member_file))) {
                    File::delete(public_path('uploads/members_files/' . $resolution->member_file));
                }
            } else {
                $filename = $resolution->member_file;
            }

            $startDate = Carbon::createFromFormat('d-m-Y g:i:A', $request->start_date)->format('Y-m-d H:i:s');
            $endDate = Carbon::createFromFormat('d-m-Y g:i:A', $request->end_date)->format('Y-m-d H:i:s');
            // $meetingDate = Carbon::createFromFormat('d-m-Y g:i:A', $request->meeting_date)->format('Y-m-d H:i:s');
            $oldActiveStatus =  $resolution->is_active;
            $resolution->update([
                'start_date' => $startDate,
                'end_date' => $endDate,
                'member_file' => $filename,
                'is_active' => $request->has('is_active') && auth()->user()->type == "0" ? 1 : 0,
                'is_modifiable' => $request->has('is_modifiable') ? 1 : 0,
                'comment_mode' => $request->has('comment_mode') ? 1 : 0,
                'voting_otp' => $request->has('voting_otp') ? 1 : 0,
                'sentemail_approval' => 'P',
                'sentemail_reportuser' => 'N',
                'is_updated' => 1
            ]);


            //resolution details add or update 
            $deleteData = ResolutionDetail::where('resolution_id', $resolutionId)->whereNotIn('id', $idArr)->get();
            $deleteOptionData = OptinonVotingDetail::where('resolution_id', $resolutionId)->whereNotIn('id', $option_ids)->get();

            // Vote::where('resolution_id', $resolutionId)->whereNotIn('resolution_details_id', $idArr)->delete();

            foreach ($deleteData as $data) {
                if (File::exists(public_path('uploads/resolution_details_files/' . $data->file_name))) {
                    File::delete(public_path('uploads/resolution_details_files/' . $data->file_name));
                }
                $data->delete();
            }


            foreach ($deleteOptionData as $data) {
                if (File::exists(public_path('uploads/option_files/' . $data->image))) {
                    File::delete(public_path('uploads/option_files/' . $data->image));
                }
                $data->delete();
            }

            $index = 0;
            foreach ($request->resolution as  $key => $resolutionData) {
                $description = $resolutionData['description'];
                $optionType = $resolutionData['option_type'];
                $min = $resolutionData['min'];
                $max = $resolutionData['max'];

                if (isset($resolutionData['resolution_id'])) {
                    $resDetails =  ResolutionDetail::find($resolutionData['resolution_id']);

                    $resFilename =  $resDetails->file_name;
                    if (isset($resolutionData['resolution_files'])) {
                        if (File::exists(public_path('uploads/resolution_details_files/' . $resDetails->file_name))) {
                            File::delete(public_path('uploads/resolution_details_files/' . $resDetails->file_name));
                        }
                        $currentFile = $resolutionData['resolution_files'];
                        $destinationPath = 'uploads/resolution_details_files';
                        $resFilename = time() . '_' . rand(1000, 9999) . '_' . $currentFile->getClientOriginalName();
                        $currentFile->move($destinationPath, $resFilename);
                    }

                    $resDetails->update([
                        'description' => $description,
                        'option_type' => $optionType,
                        'min' => $min,
                        'max' => $max,
                        'skip' => isset($resolutionData['skip']) ? 1 : 0,
                        'index' => $index + 1,
                        'file_name' => $resFilename,
                    ]);
                    foreach ($request->options[$key] as $optionData) {


                        if (isset($optionData['option_id'])) {
                            $optionDetails = OptinonVotingDetail::find($optionData['option_id']);
                            $labalFileName =  $optionDetails->image;

                            if (isset($optionData['image'])) {
                                if (File::exists(public_path('uploads/option_files/' . $labalFileName))) {
                                    File::delete(public_path('uploads/option_files/' . $labalFileName));
                                }
                                $currentFile = $optionData['image'];
                                $destinationPath = 'uploads/option_files';
                                $labalFileName = time() . '_' . rand(1000, 9999) . '_' . $currentFile->getClientOriginalName();
                                $currentFile->move($destinationPath, $labalFileName);
                            }

                            $optionDetails->update([
                                'label' =>  $optionData['name'],
                                'image' => $labalFileName,
                            ]);
                        } else {
                            $labalFileName = null;
                            if (isset($optionData['image'])) {
                                $currentFile = $optionData['image'];
                                $destinationPath = 'uploads/option_files';
                                $labalFileName = time() . '_' . rand(1000, 9999) . '_' . $currentFile->getClientOriginalName();
                                $currentFile->move($destinationPath, $labalFileName);
                            }
                            OptinonVotingDetail::create([
                                'resolution_id' => $resolution->id,
                                'resolution_details_id' => $resDetails->id,
                                'label' =>  $optionData['name'],
                                'image' => $labalFileName,
                            ]);
                        }
                    }
                } else {

                    $resFilename = null;
                    if (isset($resolutionData['resolution_files'])) {
                        $currentFile = $resolutionData['resolution_files'];
                        $destinationPath = 'uploads/resolution_details_files';
                        $resFilename = time() . '_' . rand(1000, 9999) . '_' . $currentFile->getClientOriginalName();
                        $currentFile->move($destinationPath, $resFilename);
                    }
                    $resDetails =  ResolutionDetail::create([
                        'resolution_id' => $resolution->id,
                        'description' => $description,
                        'option_type' => $optionType,
                        'min' => $min,
                        'max' => $max,
                        'skip' => isset($resolutionData['skip']) ? 1 : 0,
                        'file_name' => $resFilename,
                        'add_by' => auth()->user()->id,
                        'index' => $index + 1
                    ]);

                    foreach ($request->options[$key] as $optionData) {
                        $labalFileName = null;
                        if (isset($optionData['image'])) {
                            $currentFile = $optionData['image'];
                            $destinationPath = 'uploads/option_files';
                            $labalFileName = time() . '_' . rand(1000, 9999) . '_' . $currentFile->getClientOriginalName();
                            $currentFile->move($destinationPath, $labalFileName);
                        }
                        OptinonVotingDetail::create([
                            'resolution_id' => $resolution->id,
                            'resolution_details_id' => $resDetails->id,
                            'label' =>  $optionData['name'],
                            'image' => $labalFileName,
                        ]);
                    }
                }
                $index++;
            }
            $logData = [];
            $logData['user_id'] = auth()->user()->id;
            $logData['resolution_id'] = $resolution->id;
            $logData['action'] = "Option Voting has been created for the company '{$company->name}'.";
            addUserAction($logData);
            if ($request->has('is_active') && !$oldActiveStatus) {

                $logData = [];
                $logData['user_id'] = auth()->user()->id;
                $logData['resolution_id'] = $resolution->id;
                $logData['action'] = "Option Voting has been approved for the company '{$company->name}'.";
                addUserAction($logData);

                $mailData = [];
                $mailData['blade'] = 'emails.votingapprovel';
                $mailData['resolution'] =  $resolution;

                $mailData['subject'] = 'E-Voting Process Activated (' . $resolution->company->name . "-" . $resolution->id . ")";
                Mail::to($resolution->user->email)->send(new VoterEmail($mailData));
                Member::where(['resolution_id' => $resolution->id])->update([
                    'is_active' => $resolution->is_active
                ]);
                // Mail::to('makawanadhaval418@gmail.com')->send(new VoterEmail($data));
            } else {
                if ($resolution->is_active) {
                    $mailData = [];
                    $mailData['blade'] = 'emails.votingupdatemail';
                    $mailData['resolution'] =  $resolution;
                    $mailData['subject'] = 'E-Voting Process Is Updated (' . $resolution->company->name . "-" . $resolution->id . ")";
                    Mail::to($resolution->user->email)->send(new VoterEmail($mailData));
                    Member::where(['resolution_id' => $resolution->id])->update([
                        'is_active' => $resolution->is_active
                    ]);
                }
            }


            $logData = [];
            $logData['user_id'] = auth()->user()->id;
            $logData['resolution_id'] = $resolution->id;
            $logData['action'] = "Option Voting has been updated for the company '{$company->name}'.";
            addUserAction($logData);
            DB::commit(); // ✅ Commit changes
            return redirect()->route('voting.index')->with('status', 'Resolution updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->route('voting.index')->with('error',  $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function add_section(Request $request)
    {
        $data = [];
        $data['randomCount'] = $request->randomCount;
        return  view('app.resolution.addmore', $data);
    }
}
