<?php

namespace App\Http\Controllers;

use App\Exports\MemnberListExport;
use App\Mail\VoterEmail;
use App\Models\Member;
use App\Models\Resolution;
use App\Models\Vote;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function member_login(Request $request)
    {
        if ($request->session()->has('member_login')) {
            return redirect()->route('member.voting_list');
        }
        return view('app.member.login');
    }
    public function login(Request $request)
    {
        $loginType = $request->login_type;
        $column2 = 'otp';
        $now = Carbon::now();

        switch ($loginType) {
            case 1:
            case 'user_id':
                $loginType = 'user_name';
                $loginBy = $request->user_id;
                $column = 'user_name';
                $column2 = 'password';
                $validationRules = [
                    'user_id' => 'required',
                    'password' => 'required'
                ];

                break;

            case 2:
            case 'email':
                $loginBy = $request->email;
                $column = 'email';
                $validationRules = [
                    'email' => 'required',
                    'otp' => 'required'
                ];
                break;

            default:
                $loginBy = $request->phone;
                $column = 'phone';
                $validationRules = [
                    'phone' => 'required',
                    'otp' => 'required'
                ];
                break;
        }
        $request->validate($validationRules);

        $member = Member::where([$column => $loginBy, $column2 => $request->$column2, 'is_active' => 1])->first();
        if ($loginType != "user_name") {
            $resolutionArr = Member::where([$column => $loginBy, $column2 => $request->$column2]);
            $activeResolutions = Resolution::whereIn('id', $resolutionArr->pluck('resolution_id')->toArray())->where('start_date', '<=', $now)->where('end_date', '>=', $now)->count();

            if ($activeResolutions < 1) {
                return redirect()->route('member.login')->with('error', 'Your email address is not registered, or perhaps your voting process has not begun.');
            }
        }



        if ($member) {
            if ($request->ajax()) {
                return true;
            } else {
                $data = $logData = [];
                $logData['member_id'] = $member->id;
                $logData['resolution_id'] = $member->resolution_id;
                $logData['action'] = "Member '{$member->name}' (ID: {$member->id}) has logged in using {$loginType}.";
                addUserAction($logData);
                session(['member_login' => true, 'login_by' => $loginBy, 'login_type' => $loginType, 'login_date' => Carbon::now()]);
                return redirect()->route('member.voting_list');
            }
        } else {
            return ["error" => "We could not find a voter with the credentials you provided."];
        }
    }
    public function voting_list(Request $request)
    {
        $now = Carbon::now();
        $loginType = session('login_type');

        $activeResolutions = Resolution::whereIn('id', $this->myResolutions()->pluck('resolution_id')->toArray())->where('is_active', 1)->where('start_date', '<=', $now)->where('end_date', '>=', $now)->count();
        if ($activeResolutions < 0 && $loginType != "user_name") {
            $this->logout($request);
            return redirect()->route('member.login')->with('error', 'Your voting time is over.');
        }
        if ($request->ajax()) {
            $authUser = auth()->user();
            $loginby = session('login_by');

            $ids =  Resolution::whereIn('id', $this->myResolutions()->pluck('resolution_id')->toArray())->where('is_active', 1)->where('start_date', '<=', $now)->where('end_date', '>=', $now)->pluck('id')->toArray();

            $data = Member::whereIn('resolution_id', $ids)->where($loginType, $loginby)->where('is_active', 1)->get();
            // dd($data);
            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('id', function ($row) {
                    $now = Carbon::now();
                    $resolution = $row->resolution;
                    if (Resolution::where('id', $row->resolution_id)->where('start_date', '<=', $now)->where('end_date', '>=', $now)->count() > 0) {
                        $btn = ' <a href="' . route('member.add_voting', $row->id) . '" >' . $row->resolution->id . ' - Click here</a>';
                    } else {
                        $btn = ' <button class="btn" href="" disabled>' . $row->resolution->id . '</button>';
                    }

                    return $btn;
                })
                ->addColumn('company_name', function ($row) {
                    return $row->resolution->company->name;
                })
                ->addColumn('voter_name', function ($row) {
                    return $row->name;
                })
                ->addColumn('voting_amount', function ($row) {
                    return $row->share;
                })
                ->addColumn('start_date', function ($row) {
                    return Carbon::parse($row->resolution->start_date)->format('d-M-Y g:i A');
                })
                ->addColumn('end_date', function ($row) {
                    return Carbon::parse($row->resolution->end_date)->format('d-M-Y g:i A');
                })
                ->addColumn('holding_date', function ($row) {
                    return "Null";
                })
                ->addColumn('voting_status', function ($row) {
                    if (isset($row->vote) || count($row->option_votes)) {
                        return "VOTED";
                    } else {
                        return "NOT VOTED";
                    }
                })
                ->editColumn('voting_recipt', function ($row) {
                    if (isset($row->vote) || count($row->option_votes)) {
                        $btn = ' <a class="btn btn-success" href="' . route('vote.voting_recipt', $row->id) . '">Download</a>';
                    } else {
                        $btn = '<button class="btn btn-success" disabled>Download</button>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->escapeColumns([])
                ->make(true);
        }
        return view('app.member.member_votinglist');
    }

    public function add_voting($id)
    {
        $data = [];
        $member = Member::find($id);
        if (!$member ||  !in_array($id, $this->myResolutions()->pluck('id')->toArray())) {
            return redirect()->route('member.voting_list')->with('error', "Voter voting not found.");
        }

        $data['member_id'] = $id;
        $data['member'] = $member;

        $myResolutionsIds = $this->myResolutions()->pluck('resolution_id')->toArray();
        if (in_array($member->resolution_id, $myResolutionsIds)) {
            $now = Carbon::now();
            $data['resolution'] = Resolution::where('id', $member->resolution_id)->where('is_active', 1)->Where('start_date', '<=', $now)->Where('end_date', '>=', $now)->first();
        }
        if (isset($data['resolution']) && $data['resolution']->evsn_type == '2') {
            return view('app.member.option_voting_screen', $data);
        }

        $votes = Vote::where('member_id', $id)->get();
        if (count($votes)) {
            $data['voteArr'] = $votes;
        }

        if (isset($data['resolution'])) {
            $data['vote_count'] = Vote::where('member_id', $id)->count() == count($data['resolution']->resolution_details);
            return view('app.member.voting_screen', $data);
        }


        return redirect()->route('member.voting_list');
    }

    public function memberexist(Request $request)
    {
        $loginType = $request->login_type;
        $input = $request->input;
        $data = [];
        if ($loginType == 2) {
            $data['error'] = "Your email is not Register as a voter.";
            $member = Member::where('email', $input);
            $now = Carbon::now();

            $myVotings = Resolution::whereIn('id',  $member->pluck('resolution_id')->toArray())->where('start_date', '<=', $now)->where('end_date', '>=', $now)->count();
            if ($myVotings > 0) {
                $mailData = [];
                $otp = $this->generateOTP();
                $mailData['blade'] = 'emails.otp';
                $mailData['otp'] =  $otp;
                $mailData['subject'] = 'One-Time Password (OTP) for Account Verification';
                Mail::to($input)->send(new VoterEmail($mailData));
                $member->update([
                    "otp" => $otp
                ]);
            } else {
                return ["error" => "Your email address is not registered, or perhaps your voting process has not begun."];
            }
            $count = $myVotings;
        } elseif ($loginType == 3) {
            $data['error'] = "Your contact no. is not register as a voter.";
            $member = Member::where('phone', $input);

            $now = Carbon::now();

            $myVotings = Resolution::whereIn('id',  $member->pluck('resolution_id')->toArray())->where('start_date', '<=', $now)->where('end_date', '>=', $now)->count();

            if ($myVotings > 0) {
                $mailData = [];
                $otp = $this->generateOTP();

                $curl = curl_init();

                curl_setopt_array($curl, [
                    CURLOPT_URL => "https://control.msg91.com/api/v5/flow",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 60,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "POST",
                    CURLOPT_POSTFIELDS => json_encode([
                        "template_id" => "67693993d6fc057fb947bba4",
                        "short_url" => "1", // Use 1 for enabled, or 0 for disabled
                        "realTimeResponse" => "1",
                        "recipients" => [
                            [
                                "mobiles" => "91$input",
                                "OTP" => $otp,
                            ]
                        ]
                    ]),
                    CURLOPT_HTTPHEADER => [
                        "accept: application/json",
                        "authkey: 437167A48MMacuzvRF676932a5P1",
                        "content-type: application/json"
                    ],
                ]);

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);



                $member->update([
                    "otp" => $otp
                ]);
            } else {
                return ["error" => "Your contact no. is not register as a voter."];
                // $data['error'] = "Your contact no. is not register as a voter.";
            }

            // $member->update([
            //     "otp" => $this->generateOTP()
            // ]);
            $count = $myVotings;
            $count = $member->count();
            // 
        } else {
            $member = Member::where('user_name', $input);
            $count = $member->count();
            $data['error'] = "Authentication failure: Username is invalid.";
        }
        if ($member->count() && count($member->where('is_active', 1)->get()) <= 0) {
            $count = 0;
            $data['error'] = "The voter you are trying to login as has not yet been activated by the administrator.";
        }

        $data['count'] = $count;

        return $data;
    }

    public function resendOTP(Request $request)
    {
        $data = [];
        $input = $request->input;
        $member = Member::where('email', $input)->orWhere('phone', $input)->first();
        $data['otp'] = $member->otp;
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $mailData['blade'] = 'emails.otp';
            $mailData['otp'] = $member->otp;
            $mailData['subject'] = 'One-Time Password (OTP) for Account Verification';
            Mail::to($input)->send(new VoterEmail($mailData));
        } else {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://control.msg91.com/api/v5/flow",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode([
                    "template_id" => "67693993d6fc057fb947bba4",
                    "short_url" => "1", // Use 1 for enabled, or 0 for disabled
                    "realTimeResponse" => "1",
                    "recipients" => [
                        [
                            "mobiles" => "91$input",
                            "OTP" => $member->otp,
                        ]
                    ]
                ]),
                CURLOPT_HTTPHEADER => [
                    "accept: application/json",
                    "authkey: 437167A48MMacuzvRF676932a5P1",
                    "content-type: application/json"
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);
        }
        return $data;
    }

    public function generateOTP()
    {
        $otp = rand(100000, 999999); // Generate a random 6-digit number

        return $otp;
    }

    public function logout(Request $request)
    {
        $member = Member::where(session('login_type'), session('login_by'))->first();

        $logData['member_id'] = $member->id;
        $logData['resolution_id'] = $member->resolution_id;
        $logData['action'] = "Member '{$member->name}' (ID: {$member->id}) has logged out.";
        addUserAction($logData);

        $request->session()->flush();
        return redirect()->route('member.login');
    }

    public function index(Request $request)
    {
        $authUser = auth()->user();
        $resolutionId = $request->resolution_id;
        $resolution = Resolution::find($resolutionId);
        if (!isset($resolution) || ($authUser->type != "0" && $resolution->user_id !=  $authUser->id)) {
            return redirect()->route('voting.index');
        }

        $data['resolutionId'] = $resolutionId;
        return view('app.member.list', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'member_name' => 'required',
            'member_share' => 'required',
            'member_email' => 'required|email'
        ]);
        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
        $resolution = Resolution::find($request->resolution_id);
        if (isset($resolution)) {
            $replaceChars = [' ', '-', '&', '.', '/']; // Characters you want to replace
            $prefix = substr(str_replace($replaceChars, '_', $resolution->company->name), 0, 3);
            Member::create([
                'name' => $request->member_name,
                'email' => $request->member_email,
                'share' => $request->member_share,
                'phone' => $request->member_phone,
                'add_by' => auth()->user()->id,
                'user_name' => $prefix . time(),
                'password' => $this->generateRandomPassword(),
                'resolution_id' => $resolution->id,
                'company_id' => $resolution->company->id,
                'is_active' => $resolution->is_active
            ]);
            return ["success" => "Voter created successfully."];
        } else {
            return ["error" => "Voting not Found !"];
        }
    }

    /**
     * Display the specified resource.
     */
    public function member_list(Request $request)
    {
        if ($request->ajax()) {
            $authUser = auth()->user();

            $data = Member::where('resolution_id', $request->resolution_id)->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $editUrl = route('member.edit', $row->id);
                    $btn = '<button class="btn btn-primary btn-sm" type="button" onclick="openMemberFormModal(\'edit\', \'' . $editUrl . '\')"><i class="fa fa-edit"></i></button>&nbsp;';
                    $btn .= '<button class="btn btn-danger btn-sm" id="remove" type="button" onclick="remove_member(\'' . $row->id . '\', \'' . $row->share . '\')"><i class="fa fa-trash"></i></button>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->escapeColumns([])
                ->make(true);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $member = Member::find($id);
        $data = [];
        if (isset($member)) {
            $data['member'] = $member;
        }
        return $data;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'member_name' => 'required',
            'member_share' => 'required',
            'member_email' => 'required|email'
        ]);
        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
        $member = Member::find($request->member_id);
        if (isset($member)) {

            // if (isset($request->is_active)) {
            //     $activeType = $request->is_active;
            // } else {
            //     $activeType = $member->is_active;
            // }
            $member->update([
                'name' => $request->member_name,
                'email' => $request->member_email,
                'share' => $request->member_share,
                'phone' => $request->member_phone,
                // 'is_active' => 1
            ]);
            return ["success" => "Voter updated successfully."];
        } else {
            return ["error" => "Voting not Found !"];
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $member = Member::find($request->id);
        if (!isset($member->vote)) {
            $members = Member::where('resolution_id', $member->resolution_id)->count();
            if ($members <= 1) {
                return "At least one voter is required so you can't delete it.";
            }
            if (isset($member)) {
                $member->delete();
                return "Voter deleted successfully.";
            } else {
                return "Voter not Found!";
            }
        } else {
            return "The voter has already cast their vote, so deletion is not possible.";
        }
    }


    public function member_report(Request $request)
    {
        if ($request->ajax() && isset($request->resolution_id)) {
            $data = Member::where('resolution_id', $request->resolution_id)->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('email_sent', function ($row) {
                    return $row->email_sent == "N" ? "NOT SENT" : "SENT";
                })
                ->addColumn('sent_date', function ($row) {
                    return  isset($row->sent_date) ? Carbon::parse($row->sent_date)->format('d-M-Y g:i A') : "-";
                })
                ->addColumn('delivery_date', function ($row) {
                    return isset($row->delivery_date) ? Carbon::parse($row->delivery_date)->format('d-M-Y g:i A') : "-";
                })
                ->addColumn('resend_btn_sms', function ($row) {
                    $now = Carbon::now();
                    $resolution = $row->resolution;
                    if (isset($row->phone) && Resolution::where('id', $resolution->id)->where('id', $resolution->id)->where('is_active', 1)->where('end_date', '>=', $now)->exists()) {
                        $btn = '<a class="btn btn-primary btn-xs" href="' . route('member.sms', $row->id) . '">SMS</a>';
                    } else {
                        $btn = '<button class="btn btn-primary btn-xs" disabled>Resend</button>';
                    }
                    return $btn;
                })

                ->addColumn('resend_btn', function ($row) {
                    $now = Carbon::now();
                    $resolution = $row->resolution;
                    if (Resolution::where('id', $resolution->id)->where('id', $resolution->id)->where('is_active', 1)->where('end_date', '>=', $now)->exists()) {
                        $btn = '<a class="btn btn-primary btn-xs" href="' . route('member.resend_mail', $row->id) . '">Resend</a>';
                    } else {
                        $btn = '<button class="btn btn-primary btn-xs" disabled>Resend</button>';
                    }
                    return $btn;
                })
                ->addColumn('edit_btn', function ($row) {
                    $now = Carbon::now();
                    $resolution = $row->resolution;
                    $editUrl = route('member.edit', $row->id);
                    if (Resolution::where('id', $resolution->id)->where('id', $resolution->id)->where('is_active', 1)->where('end_date', '>=', $now)->exists()) {
                        $btn = '<button class="btn btn-primary btn-xs" onclick="openMemberFormModal( \'' . $editUrl . '\')">Edit</button>';
                    } else {
                        $btn = ' <button class="btn btn-primary btn-xs" href="" disabled>Edit</button>';
                    }
                    return $btn;
                })
                ->rawColumns(['action'])
                ->escapeColumns([])
                ->make(true);
        }
    }

    public function myResolutions()
    {
        $loginType = session('login_type');
        $loginby = session('login_by');
        $resolutionArr = Member::where($loginType, $loginby)->where('is_active', 1);
        return $resolutionArr;
    }


    public function resend_mail($id)
    {
        try {
            $member =  Member::find($id);
            $member->update([
                'sent_date' => Carbon::now(),
                'delivery_date' => Carbon::now(),
                'email_sent' => 'Y'
            ]);

            $data['member'] = $member;
            $data['blade'] = 'emails.voter_email';
            $data['subject'] = 'Details of Voting of (' . $member->company->name . ")";

            try {
                Mail::to($member->email)->send(new VoterEmail($data));
                $member->update([
                    'reason' => 'Delivery'
                ]);
            } catch (Exception $e) {

                $member->update([
                    'reason' => $e->getMessage()
                ]);
                return redirect()->back()->with('error', 'something went wrong.');
            }
            $logData['user_id'] = auth()->user()->id;

            $logData['resolution_id'] = $member->resolution_id;
            // If the email is sent successfully, log the action
            $logData['action'] = "Login and password email sent to member '{$member->email}' (ID: {$member->id}).";
            addUserAction($logData);

            return redirect()->back()->with('status', 'Mail send to voter successfully.');
        } catch (\Exception $e) {
            // Handle the exception and redirect back with an error message
            return redirect()->back()->with('error', 'something went wrong.');
        }
    }

    public function resend_sms($id)
    {
        try {
            $member =  Member::find($id);
            if (isset($member->phone)) {
                // $curl = curl_init();

                // curl_setopt_array($curl, [
                //     CURLOPT_URL => "https://control.msg91.com/api/v5/flow",
                //     CURLOPT_RETURNTRANSFER => true,
                //     CURLOPT_ENCODING => "",
                //     CURLOPT_MAXREDIRS => 10,
                //     CURLOPT_TIMEOUT => 60,
                //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                //     CURLOPT_CUSTOMREQUEST => "POST",
                //     CURLOPT_POSTFIELDS => json_encode([
                //         "template_id" => "6788ab22d6fc05493a718772",
                //         "realTimeResponse" => "1",
                //         "recipients" => [
                //             [
                //                 "mobiles" => "91$member->phone",
                //                 "Name" =>  $member->company->name,
                //                 "DateTime" =>  Carbon::createFromFormat('Y-m-d H:i:s', $member->resolution->start_date)->format('d-M-Y h:i A')
                //             ]
                //         ]
                //     ]),
                //     CURLOPT_HTTPHEADER => [
                //         "accept: application/json",
                //         "authkey: 437167A48MMacuzvRF676932a5P1",
                //         "content-type: application/json"
                //     ],
                // ]);

                // $response = curl_exec($curl);
                // $err = curl_error($curl);

                // curl_close($curl);
            }

            return redirect()->back()->with('status', 'SMS send to voter successfully.');
        } catch (\Exception $e) {
            // Handle the exception and redirect back with an error message
            return redirect()->back()->with('error', 'something went wrong.');
        }
    }




    public function exportData(Request $request, $id)
    {
        $data = $request->selected_fields;
        return Excel::download(new MemnberListExport($id, $data), 'memberlist-' . $id . '.csv');
    }

    public function change_password()
    {
        if (session('login_type')  == 'user_name') {
            return view('app.member.change_password');
        } else {
            return redirect()->route('member.voting_list')->with('error', 'Permission denied.');
        }
    }

    public function update_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->route('member.change_password')
                ->withErrors($validator)
                ->withInput();
        }

        $loginType = session('login_type');
        $loginby = session('login_by');
        $member = Member::where($loginType, $loginby)->where('is_active', 1)->first();

        $member->update([
            'password' => $request->new_password
        ]);

        $logData['member_id'] = $member->id;
        $logData['resolution_id'] = $member->resolution_id;
        $logData['action'] = "Member '{$member->name}' (ID: {$member->id}) has updated their password.";
        addUserAction($logData);

        return redirect()->route('member.voting_list')->with('status', 'Password changed successfully!');
    }
}
