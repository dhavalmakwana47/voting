<?php

namespace App\Http\Controllers;

use App\Exports\MemberSampleExcel;
use App\Imports\ConvertToArray;
use App\Mail\VoterEmail;
use App\Models\Company;
use App\Models\Resolution;
use App\Models\UserCompanyMap;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Member;
use App\Models\ResolutionDetail;
use App\Models\Vote;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;

class ResolutionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();

            if ($authUser->type != "0") {
                $data = Resolution::where('user_id', $authUser->id)->get();
            } else {
                $data = Resolution::all();
            }
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = "";
                    $editRoute = $row->evsn_type == 2 ? route('option-voting.edit', $row->id) : route('voting.edit', $row->id);
                    if ((auth()->user()->type == "0") || (!$row->is_active)) {
                        if (auth()->user()->type == "0") {
                            $btn .= '<a onClick="deleteResolution(' . $row->id . ')" class="btn btn-danger btn-xs deleteconfirm"><i class="fa fa-trash"></i></a>';
                        }
                        $btn .= ' <a href="' . $editRoute . '" class="btn btn-warning btn-xs"><i class="fa fa-pencil-alt"></i></a>';
                    } else {

                        $btn .= ' <a href="' . $editRoute . '" class="btn btn-warning btn-xs"><i class="fa fa-pencil-alt"></i></a>';
                    }
                    return $btn;
                })
                ->addColumn('resolution_type', function ($row) {
                    if ($row->evsn_type == "0") {
                        return 'Resolution';
                    } elseif ($row->evsn_type == "1") {
                        return 'Instruction';
                    } else {
                        return 'Option';
                    }
                })
                ->addColumn('member_report', function ($row) {
                    $memberReportRoute = route('member.index', $row->id);
                    if ($row->is_active) {
                        $btn = '<a href="' . $memberReportRoute . '" class="btn btn-success btn-xs" >Mail Delivery</a>';
                    } else {
                        $btn = '<button  class="btn btn-success btn-xs" disabled="disabled">Mail Delivery</button>';
                    }
                    return $btn;
                })
                ->addColumn('start_date', function ($row) {
                    return Carbon::parse($row->start_date)->format('d-M-Y g:i A');
                })
                ->addColumn('end_date', function ($row) {
                    return Carbon::parse($row->end_date)->format('d-M-Y g:i A');
                })
                // ->addColumn('meeting_date', function ($row) {
                //     return Carbon::parse($row->meeting_date)->format('d-M-Y g:i A');
                // })
                ->addColumn('created_at_modify', function ($row) {
                    return Carbon::parse($row->created_at)->format('d-M-Y g:i A');
                })
                // ->addColumn('user_name', function ($row) {
                //     return $row->user->name;
                // })
                ->addColumn('company_name', function ($row) {
                    return $row->company->name;
                })
                ->editColumn('is_active', function ($row) {
                    // if (auth()->user()->type == "0") {
                    //     if ($row->is_active == 1) {
                    //         return '<input type="checkbox"  name="my-checkbox" checked data-bootstrap-switch="" onChange="changeResolutionStatus(' . $row->id . ')">';
                    //     } else {
                    //         return '<input type="checkbox"  name="my-checkbox"  data-bootstrap-switch="" onChange="changeResolutionStatus(' . $row->id . ')">';
                    //     }
                    // } else {
                    if ($row->is_active == 1) {
                        return 'Approved';
                    } else {
                        return 'Pending';
                    }
                    // }
                })
                ->rawColumns(['action'])
                ->escapeColumns([])
                ->make(true);
        }
        return view('app.resolution.list');
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
        return view('app.resolution.addupdate', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'resolution_files.*' => 'required|mimes:pdf',
            'description.*' => 'required',
            'company' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'meeting_details' => 'nullable|max:200',
            'member_file' => 'required',
            'evsn_type' => 'required',
        ]);

        $validator->after(function ($validator) use ($request) {
            if (!isset($request->resolution_files)) {
                $validator->errors()->add('resolution_files.0', 'The resolution_files field is required please reupload.');
            } else {
                if (count($request->resolution_files) != count($request->description)) {
                    $validator->errors()->add('resolution_files.0', 'The resolution_files is missing please reupload.');
                }
            }
        });

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
        // try {

        $resolutionFilsArr = $request->file('resolution_files');
        $resolutionDesArr = $request->description;


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
            'evsn_type' => $request->evsn_type,
            'start_date' => Carbon::createFromFormat('d-m-Y g:i:A', $request->start_date)->format('Y-m-d H:i:s'),
            'end_date' => Carbon::createFromFormat('d-m-Y g:i:A', $request->end_date)->format('Y-m-d H:i:s'),
            'member_file' => $filename,
            'meeting_details' => $request->meeting_details,
            'is_modifiable' => $request->has('is_modifiable') ? 1 : 0
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
        foreach ($resolutionDesArr as  $description) {
            $currentFile = $resolutionFilsArr[$index];

            $destinationPath = 'uploads/resolution_details_files';
            $resFilename = time() . '_' . rand(1000, 9999) . '_' . $currentFile->getClientOriginalName();
            $currentFile->move($destinationPath, $resFilename);

            ResolutionDetail::create([
                'resolution_id' => $resolution->id,
                'description' => $description,
                'file_name' => $resFilename,
                'add_by' => auth()->user()->id,
                'index' => $index + 1

            ]);
            $index++;
        }
        $logData = [];
        $logData['user_id'] = auth()->user()->id;
        $logData['resolution_id'] = $resolution->id;
        $logData['action'] = "Normal Voting has been created for the company '{$company->name}'.";
        addUserAction($logData);
        return redirect()->route('voting.index')->with('status', 'Resolution added successfully');
        // } catch (\Exception $e) {
        //     // Handle the exception and redirect back with an error message
        //     return redirect()->back()->with('error', 'something went wrong.');
        // }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resolution $voting)
    {

        $data = [];
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
            return view('app.resolution.addupdate', $data);
        } else {
            return redirect()->route('voting.index');
        }
    }
    public function update(Request $request)
    {
        // dd($request);
        $resolutionId = $request->id;
        $resolution = Resolution::find($resolutionId);
        // Retrieve the IDs of the members who have voted on this resolution
        $votedMemberIds = $resolution->votes->pluck('member_id');

        // Update the 'email_sent' field to 'N' only for members who have not voted
        $resolution->members()
            ->whereNotIn('id', $votedMemberIds)
            ->update(['email_sent' => 'N']);

        // $resolution->members()->update(['email_sent' => 'N']);
        if ($resolution->is_active == 1 && auth()->user()->type != '0') {
            $validator = Validator::make($request->all(), [
                'end_date' => 'required|date_format:d-m-Y g:i:A|after:start_date',
            ]);
            $endDate = Carbon::createFromFormat('d-m-Y g:i:A', $request->end_date)->format('Y-m-d H:i:s');
            $validator->validate();
            $resolution->update([
                'end_date' => $endDate
            ]);
            return redirect()->route('voting.index')->with('status', 'Resolution updated successfully');
        }

        $validator = Validator::make($request->all(), [
            'resolution_files.*' => 'required|mimes:pdf|max:5120', // max:5120 means max 5 MB, adjust as needed
            'description.*' => 'required',
            'start_date' => 'required|date_format:d-m-Y g:i:A|before:end_date',
            'end_date' => 'required|date_format:d-m-Y g:i:A|after:start_date',
            'meeting_details' => 'nullable|max:200'
        ]);

        $idArr =  array_map('intval', $request->resolution_details_id);
        $deleteData = ResolutionDetail::where('resolution_id', $resolutionId)->whereNotIn('id', $idArr)->get();

        $validator->after(function ($validator) use ($request) {
            if (!isset($request->resolution_files) && !isset($request->resolution_details_id)) {
                $validator->errors()->add('resolution_files.0', 'The resolution_files field is required please upload.');
            }
        });

        $validator->validate();
        try {

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
                'meeting_details' => $request->meeting_details,
                'is_active' => $request->has('is_active') && auth()->user()->type == "0" ? 1 : 0,
                'is_modifiable' => $request->has('is_modifiable') ? 1 : 0,
                'sentemail_approval' => 'P',
                'sentemail_reportuser' => 'N',
                'is_updated' => 1
            ]);


            //resolution details add or update 
            $resolutionFilsArr = isset($request->resolution_files) ? array_merge($request->file('resolution_files'), []) : [];
            $resolutionDesArr = array_merge($request->description, []);
            $deleteData = ResolutionDetail::where('resolution_id', $resolutionId)->whereNotIn('id', $idArr)->get();
            Vote::where('resolution_id', $resolutionId)->whereNotIn('resolution_details_id', $idArr)->delete();
            foreach ($deleteData as $data) {
                if (File::exists(public_path('uploads/resolution_details_files/' . $data->file_name))) {
                    File::delete(public_path('uploads/resolution_details_files/' . $data->file_name));
                }
                $data->delete();
            }

            $index = 0;
            $fileIndex = 0;
            foreach ($idArr as  $id) {
                $description = $resolutionDesArr[$index];
                if ($id == 0) {
                    $currentFile = $resolutionFilsArr[$fileIndex];
                    $destinationPath = 'uploads/resolution_details_files';
                    $resFilename =  time() . '_' . rand(1000, 9999) . '_' . $currentFile->getClientOriginalName();
                    $currentFile->move($destinationPath, $resFilename);

                    ResolutionDetail::create([
                        'resolution_id' => $resolutionId,
                        'description' => $description,
                        'file_name' => $resFilename,
                        'add_by' => auth()->user()->id,
                        'index' => $index + 1

                    ]);

                    $fileIndex++;
                } else {
                    $resData =  ResolutionDetail::find($id);
                    $updateData = [
                        'description' => $description,
                        'index' => $index + 1,
                    ];
                    if (isset($request->resolution_files[$id])) {
                        $currentFile = $request->resolution_files[$id];
                        $destinationPath = 'uploads/resolution_details_files';
                        $resFilename = time() . '_' . rand(1000, 9999) . '_' . $currentFile->getClientOriginalName();
                        $currentFile->move($destinationPath, $resFilename);
                        $updateData['file_name'] = $resFilename;

                        if (File::exists(public_path('uploads/resolution_details_files/' . $resData->file_name))) {
                            File::delete(public_path('uploads/resolution_details_files/' . $resData->file_name));
                        }
                        $fileIndex++;
                    }

                    $resData->update($updateData);
                }
                $index++;
            }
            $logData = [];
            $logData['user_id'] = auth()->user()->id;
            $logData['resolution_id'] = $resolution->id;
            $logData['action'] = "Normal Voting has been upadted for the company '{$company->name}'.";
            addUserAction($logData);

            if ($request->has('is_active') && !$oldActiveStatus) {

                $logData = [];
                $logData['user_id'] = auth()->user()->id;
                $logData['resolution_id'] = $resolution->id;
                $logData['action'] = "Normal Voting has been approved for the company '{$company->name}'.";
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
            $logData['action'] = "Normal Voting has been updated for the company '{$company->name}'.";
            addUserAction($logData);
            return redirect()->route('voting.index')->with('status', 'Resolution updated successfully');
        } catch (\Exception $e) {
            // Handle the exception and redirect back with an error message
            return redirect()->back()->with('error', 'something went wrong.');
        }
    }


    /**
     * Update the specified resource in storage.
     */
    public function destroy(string $id)
    {
        $resolution = Resolution::find($id);
        if (!$resolution->votes->count()) {
            if (auth()->user()->type == "0" ||  ($resolution->user_id == auth()->user()->id &&  !($resolution->is_active))) {

                if (File::exists(public_path('uploads/members_files/' . $resolution->member_file))) {
                    File::delete(public_path('uploads/members_files/' . $resolution->member_file));
                }
                Member::where('resolution_id', $resolution->id)->delete();
                $deleteData = ResolutionDetail::where('resolution_id', $resolution->id)->get();
                foreach ($deleteData as $data) {
                    if (File::exists(public_path('uploads/resolution_details_files/' . $data->file_name))) {
                        File::delete(public_path('uploads/resolution_details_files/' . $data->file_name));
                    }
                    $data->delete();
                }
                if (isset($resolution)) {
                    $resolution->delete();
                }
                return "Resolution deleted successfully";
            } else {
                return "You didn't have permission to delete this voting.";
            }
        }
        return "The voting has been generated, so you can't delete it";
    }

    /**
     * Remove the specified resource from storage.
     */
    public function changeStatus(Request $request)
    {
        $resolution = Resolution::find($request->id);
        Member::where('resolution_id', $resolution->id)->update([
            "is_active" => $resolution->is_active ? 0 : 1
        ]);

        $resolution->update([
            "is_active" => $resolution->is_active ? 0 : 1
        ]);
    }

    public function upload(Request $request)
    {
        $rules = array(
            'member_file'  => 'required|mimes:xlsx,xls,ods|max:5120'
        );

        $fileValidate = Validator::make($request->all(), $rules);
        if ($fileValidate->fails()) {
            return  $data['excel_error'] = $fileValidate->errors();
        }

        $file = $request->file('member_file');

        $data = Excel::toCollection(new ConvertToArray(), $file);
        $data = $data->toArray()[0];

        $arrfield = ['name', 'share', 'email', 'phone'];
        $uplodedFieldArr = [];

        $index = 0;
        foreach ($data[0] as $key => $value) {
            if ($index > 3) {
                break;
            }
            array_push($uplodedFieldArr, $key);
            $index++;
        }
        if (!($arrfield == $uplodedFieldArr)) {
            return  ['member_file' => ["Title/ Column Header not found kindly verify your excel format."]];
        }

        $rules = [
            '*.name' => 'required|string|max:255',
            '*.email' => 'required|email',
            '*.share' => ['required', 'regex:/^\d+(\.\d+)?$/'],
        ];

        $messages = [
            '*.name.required' => 'The name field is required.',
            '*.name.string'   => 'The name must be a string.',
            '*.email.required' => 'The email field is required.',
            '*.email.email'   => 'The email must be a valid email address.',
            '*.share.required' => 'The share field is required.',
            '*.share.regex'   => 'The share must be a valid number.',
        ];

        $validator = Validator::make($data, $rules, $messages);

        // Check if validation fails
        if ($validator->fails()) {
            $dataArr['errors'] = $validator->errors()->toArray();
            $dataArr['data'] = $data;
            return $dataArr;
            // Handle validation errors as needed
        } else {
            $dataArr['data'] = $data;
            return $dataArr;
        }
    }
    public function downloadFile()
    {
        return Excel::download(new MemberSampleExcel, 'sample.xlsx');
    }

    public function resolutionDetailsFile($id)
    {
        try {
            $id = Crypt::decrypt($id);
            $resolutionDetails = ResolutionDetail::find($id);
            if (isset($resolutionDetails)) {
                $filePath = public_path("uploads/resolution_details_files/" . $resolutionDetails->file_name);
                return response()->download($filePath);
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }
}
