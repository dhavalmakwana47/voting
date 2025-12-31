<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Mail\VoterEmail;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $data = User::where('type', '!=', "0")->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a onClick="deleteUser(' . $row->id . ')" class="btn btn-danger btn-xs deleteconfirm"><i class="fa fa-trash"></i></a>';
                    $btn .= ' <a href="' . route('users.edit', $row->id) . '" class="btn btn-warning btn-xs"><i class="fa fa-pencil-alt"></i></a>';
                    return $btn;
                })
                ->editColumn('is_active', function ($row) {
                    return $row->is_active ? "Approved" : "Pending";
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $row->created_at)->format('d-M-Y h:i A');
                })
                ->editColumn('type', function ($row) {
                    if ($row->user_type == "1") {
                        return 'AR';
                    } else {
                        return 'Scrutinizer';
                    }
                })
                ->rawColumns(['action'])
                ->escapeColumns([])
                ->make(true);
        }
        return view('app.users.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('app.users.addedit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        $user = auth()->user();

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'pan_number' => $request->pan_number,
            'phone' => $request->phone,
            'user_type' =>  $request->user_type,
            'is_active' => isset($request->is_active) ?: 0,
            'password' => Hash::make($request->password),
            'password_view' => $request->password
        ]);

        $logData['user_id'] = $user->id;
        $logData['resolution_id'] = 0;

        $data = [];
        $data['name'] = $request->name;
        $data['email'] = $request->email;
        $data['password'] = $request->password;
        $data['blade'] = 'emails.userlogindetails';
        $data['subject'] = 'Welcome to India E-Voting! Your Registration is successfully ';
        // Mail::to('makawanadhaval418@gmail.com')->send(new VoterEmail($data));
        try {
            // Send the email
            Mail::to($request->email)->send(new VoterEmail($data));

            // If the email is sent successfully, log the action
            $logData['action'] = "New user '{$request->name}' email password mail sent";
            addUserAction($logData);
        } catch (\Exception $e) {
            // If there is an error, log that the email was not sent
            $logData['action'] = "Failed to send email to '{$request->email}': " . $e->getMessage();
            addUserAction($logData);
        };

        $logData['action'] = "New user '{$request->name}' has been created";
        addUserAction($logData);


        return redirect()->route('users.index')->with('status', 'User created successfully');;
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
    public function edit(User $user)
    {
        if ($user->type != 0) {
            $data = [];
            $data['user'] = $user;

            return view('app.users.addedit', $data);
        } else {
            return redirect()->route('users.index');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, string $id)
    {
        $updateArr = [
            'name' => $request->name,
            'email' => $request->email,
            'pan_number' => $request->pan_number,
            'phone' => $request->phone,
            'user_type' =>  $request->user_type,
            'is_active' => isset($request->is_active) ?: 0
        ];
        if (!empty($request->password)) {
            $updateArr['password'] = Hash::make($request->password);
            $updateArr['password_view'] = $request->password;
        }
        $userData = User::find($request->id);

        $user = auth()->user();
        $logData['user_id'] = $user->id;
        $logData['resolution_id'] = 0;

        if ($request->email != $userData->email) {
            $logData['action'] = "The email for user '{$userData->email}' has been updated to '{$request->email}'.";
            addUserAction($logData);
        }


        $logData['action'] = "The profile of '{$userData->name}' has been updated.";
        addUserAction($logData);
        $userData->update($updateArr);



        return redirect()->route('users.index')->with('status', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (isset($user)) {
            $user->delete();
        }
        return true;
    }

    public function changeStatus(Request $request)
    {
        $user = User::find($request->id);
        $user->update([
            "is_active" => $user->is_active ? 0 : 1
        ]);
    }

    public function showChangePasswordForm()
    {
        return view('app.users.change-password');
    }

    public function updatePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {

            if (auth()->user()->type == '2') {

                return redirect()->route('bidderpassword.change')
                    ->withErrors($validator)
                    ->withInput();
            } else {
                return redirect()->route('userpassword.change')
                    ->withErrors($validator)
                    ->withInput();
            }
        }



        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();



        return redirect()->route('userpassword.change')->with('status', 'Password changed successfully.');
    }
}
