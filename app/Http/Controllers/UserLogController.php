<?php

namespace App\Http\Controllers;

use App\Exports\UsersLogExport;
use App\Models\Company;
use App\Models\Resolution;
use App\Models\User;
use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;

class UserLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = [];
        if ($request->ajax()) {
            $user = auth()->user();
            if ($user->type == 0) {
                $data = UserLog::with('resolution', 'member', 'user');
            } else {
                $resolutions = Resolution::where('user_id', $user->id)->pluck('id')->toArray();
                $data = UserLog::with('resolution', 'member', 'user')->whereIn('resolution_id', $resolutions);
            }
            return Datatables::of($data)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $row->created_at)->format('d-M-Y h:i A');
                })
                ->addColumn('user_type', function ($row) {
                    if (isset($row->member)) {
                        return "Voter";
                    }
                    $type = 'Admin';
                    if (isset($row->user) && $row->user->user_type == 1) {
                        $type = 'AR';
                    } elseif (isset($row->user) && $row->user->user_type == 2) {
                        $type = 'Scrutinizer';
                    }

                    return $type;
                })

                ->addColumn('member_id', function ($row) {
                    return isset($row->member) ? $row->member->user_name : '';
                })
                ->addColumn('user_name', function ($row) {
                    if (isset($row->member->name)) {
                        return $row->member->name;
                    } elseif (isset($row->user->name)) {
                        return $row->user->name;
                    }else{
                        return "";
                    }
                })
                ->filter(function ($query) use ($request) {
                    if ($request->has('user_id') && $request->user_id != "") {
                        if (User::find($request->user_id)->type != 0) {
                            $query->where('user_id', $request->user_id);
                        }
                    }
                    if ($request->has('voting_id') && $request->voting_id != "") {
                        $query->where('resolution_id', $request->voting_id);
                    } else {
                        $query->where('member_id', null);
                    }
                })

                ->rawColumns(['user'])
                ->escapeColumns([])
                ->make(true);
        }
        return view('app.userlog.list', $data);
    }

    public function getusers(Request $request)
    {
        $userType = $request->user_type;
        if ($userType == 'admin') {
            $users = User::where('type', '0')->get();
        } elseif ($userType == 'scrutinizer') {
            $users = User::where('user_type', 2)->get();
        } elseif ($userType == 'ar') {
            $users = User::where('user_type', 1)->get();
        } else {
            $users = [];
        }
        $data['users'] = $users;

        return view('app.dd-html.userdropdown', $data);
    }

    public function get_votings(Request $request)
    {

        $user = User::find($request->user_id);
        if ($user->type == 0) {
            $votings = Resolution::all();
        } else {
            $votings = Resolution::where('user_id', $request->user_id)->get();
        }

        $data['votingrArr'] = $votings;

        return view('app.dd-html.voting_list', $data);
    }

    public function userlog_downlaod(Request $request)
    {
        return Excel::download(new UsersLogExport($request), 'userslogs.xlsx');


    }
}
