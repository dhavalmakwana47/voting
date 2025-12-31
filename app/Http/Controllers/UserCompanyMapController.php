<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserCompanyMapRequest;
use App\Models\Company;
use App\Models\User;
use App\Models\UserCompanyMap;
use Illuminate\Http\Request;

class UserCompanyMapController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = [];
        $data['companyArr'] = Company::where([['is_active', 1]])->orderBy('id','desc')->get();
        $data['scrutinizerArr'] = User::where([['is_active', 1], ['user_type', 2], ['type', "!=", "0"]])->orderBy('id','desc')->get();
        $data['authorizedPersonArr'] =  User::where([['is_active', 1], ['user_type', 1], ['type', "!=", "0"]])->orderBy('id','desc')->get();

        return view('app.usercompanymap.addupdate', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUserCompanyMapRequest $request)
    {
        $scrutinizerArr = $request->scrutinizer;
        $arArr = $request->ar;
        $company = $request->company;
        if (isset($request->ar) && isset($request->scrutinizer)) {
            $userArr = array_merge($scrutinizerArr, $arArr);
        }else{
            $userArr = isset($request->scrutinizer) ? $request->scrutinizer : $request->ar;
        }
        UserCompanyMap::where('company_id',$company)->delete();
        foreach ($userArr as $user) {
            UserCompanyMap::create([
                'user_id' => $user,
                'company_id' => $company,
                'add_by' => auth()->user()->id,
                'update_by' => auth()->user()->id,
                'is_active' => 1
            ]);
        }
        return redirect()->route('usercompanymap.create')->with('status', 'Comany assign to user successfully');
    }

    public function assign_users(Request $request)
    {
        $data = [];
        $data['users'] = UserCompanyMap::where('company_id', $request->company_id)->pluck('user_id')->toArray();
        return $data;
    }
}
