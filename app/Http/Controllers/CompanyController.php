<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Requests\CreateCompanyRequest;
use Carbon\Carbon;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Company::all();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<a onClick="deleteUser(' . $row->id . ')" class="btn btn-danger btn-xs deleteconfirm"><i class="fa fa-trash"></i></a>';
                    $btn .= ' <a href="' . route('company.edit', $row->id) . '" class="btn btn-warning btn-xs"><i class="fa fa-pencil-alt"></i></a>';
                    return $btn;
                })
                ->editColumn('is_active', function ($row) {
                    // if ($row->is_active == 1) {
                    //     return '<input type="checkbox"  name="my-checkbox" checked data-bootstrap-switch="" onChange="changeUserStatus(' . $row->id . ')">';
                    // } else {
                    //     return '<input type="checkbox"  name="my-checkbox"  data-bootstrap-switch="" onChange="changeUserStatus(' . $row->id . ')">';
                    // }
                    return $row->is_active ? "Approved" : "Pending";
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $row->created_at)->format('d-M-Y h:i A');
                })
                ->rawColumns(['action'])
                ->escapeColumns([])
                ->make(true);
        }
        return view('app.company.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('app.company.edit');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCompanyRequest $request)
    {
        Company::create([
            'name' => $request->company_name,
            'email' => $request->company_email,
            'cin' => $request->company_cin,
            'subscriptions_period' => $request->subscriptions_period,
            'is_active' => isset($request->is_active) ? 1 : 0,
        ]);

        $user = auth()->user();
        $logData['user_id'] = $user->id;
        $logData['resolution_id'] = 0;
        $logData['action'] = "New company '{$request->company_name}' has been created";
        addUserAction($logData);

        return redirect()->route('company.index')->with('status', 'Company updated successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        // dd($company->cin);
        $data = [];
        $data['company'] = $company;
        return view('app.company.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CreateCompanyRequest $request)
    {
        $company = Company::find($request->id);

        $user = auth()->user();
        $logData['user_id'] = $user->id;
        $logData['resolution_id'] = 0;
        $logData['action'] = "Company details for '{$company->name}' have been updated.";
        addUserAction($logData);

        $company->update([
            'name' => $request->company_name,
            'email' => $request->company_email,
            'cin' => $request->company_cin,
            'subscriptions_period' => $request->subscriptions_period,
            'is_active' => isset($request->is_active) ? 1 : 0,
        ]);
        return redirect()->route('company.index')->with('status', 'Company updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        if (isset($company)) {
            $company->delete();
        }
        return true;
    }

    public function changeStatus(Request $request)
    {
        $company = Company::find($request->id);
        $company->update([
            "is_active" => $company->is_active ? 0 : 1
        ]);
    }

    public function addcompany()
    {
        return view('app.company.add');
    }

    public function storecompany(CreateCompanyRequest $request)
    {
        Company::create([
            'name' => $request->company_name,
            'email' => $request->company_email,
            'cin' => $request->company_cin,
            'subscriptions_period' => $request->subscriptions_period,
            'is_active' => 0,
        ]);
        return redirect()->back();
    }
}
