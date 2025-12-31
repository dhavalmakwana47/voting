<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Resolution;
use App\Models\User;
use App\Models\UserCompanyMap;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $data = [];
        $user = auth()->user();
        if ($user->type == '0') {
            $data['votingCount'] = Resolution::count();
            $data['userCount'] = User::count();
            $data['companyCount'] = Company::count();
        } else {
            $data['votingCount'] = Resolution::where('user_id', $user->id)->count();
            $data['companyCount'] = UserCompanyMap::where('user_id', $user->id)->count();
        }
        return view('app.index', $data);
    }
}
