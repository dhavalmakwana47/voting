<?php

namespace App\Http\Controllers;

use App\Exports\multisheetExport;
use App\Exports\VotingReportExport;
use App\Models\Company;
use App\Models\Member;
use App\Models\Resolution;
use App\Models\UserCompanyMap;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\DataTables;
use Dompdf\Dompdf;
use Dompdf\Options;

class VotingReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $authUser = auth()->user();

        if ($request->ajax()) {

            if ($authUser->id == 76) {
                $data = Resolution::where('user_id', 76)
                    ->where('end_date', '<=', Carbon::now())
                    ->orderBy('id', 'desc');
            } elseif ($authUser->user_type == "1") {
                $data = Resolution::where('user_id', $authUser->id)->where('end_date', '<=', Carbon::now())->orderBy('id', 'desc');
            } elseif ($authUser->type != "0") {
                $data = Resolution::where('user_id', $authUser->id)->orderBy('id', 'desc');
            } else {
                $data = Resolution::query()->orderBy('id', 'desc');
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('download', function ($row) {
                    if ($row->evsn_type == '2') {
                        $route = route('option_report.get_report', ['type' => 'csv', 'id' => $row->id]);
                    } else {
                        $route = route('votingreport.get_report', ['type' => 'csv', 'id' => $row->id]);
                    }
                    $btn = ' <a href="' . $route . '"  class="btn btn-primary" >Excel</a>';
                    return $btn;
                })
                ->addColumn('new_report', function ($row) {
                    if ($row->evsn_type == '2') {
                        $btn = '';
                    } else {
                        $route = route('votingreport.new_report', ['id' => $row->id]);
                        $btn = ' <a href="' . $route . '"  class="btn btn-success" >PDF</a>';
                    }
                    return $btn;
                })
                ->addColumn('new_report_view', function ($row) {
                    if ($row->evsn_type == '2') {
                        $btn = '';
                    } else {
                        $route = route('votingreport.new_report_view', ['id' => $row->id]);
                        $btn = ' <a href="' . $route . '"  class="btn btn-info" >View</a>';
                    }
                    return $btn;
                })
                ->addColumn('pdf', function ($row) {
                    if ($row->evsn_type == '2') {
                        $route = route('option_report.get_report', ['type' => 'pdf', 'id' => $row->id]);
                    } else {
                        $route = route('votingreport.get_report', ['type' => 'pdf', 'id' => $row->id]);
                    }
                    $btn = ' <a href="' . $route . '"  class="btn btn-success" >PDF</a>';
                    return $btn;
                })
                ->addColumn('reportpdf', function ($row) {
                    if ($row->evsn_type == '2') {
                        $route = route('option_report.get_report', ['type' => 'pdf', 'id' => $row->id]);
                    } else {
                        $route = route('votingreport.get_report', ['type' => 'reportpdf', 'id' => $row->id]);
                    }
                    $btn = ' <a href="' . $route . '"  class="btn btn-success" >PDF</a>';
                    return $btn;
                })
                ->addColumn('start_date', function ($row) {
                    return Carbon::parse($row->start_date)->format('d-M-Y g:i A');
                })
                ->addColumn('end_date', function ($row) {
                    return Carbon::parse($row->end_date)->format('d-M-Y g:i A');
                })
                ->addColumn('user_name', function ($row) {
                    return $row->user->name;
                })
                ->addColumn('company_name', function ($row) {
                    return $row->company->name;
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('company') != "") {
                        $instance->where('company_id', $request->get('company'));
                    }
                    if (!empty($request->get('search'))) {
                        $instance->where(function ($w) use ($request) {
                            $search = $request->get('search');
                            $w->orWhere('id', 'LIKE', "%$search%");
                        });
                    }
                })

                ->rawColumns(['action'])
                ->escapeColumns([])
                ->make(true);
        }
        $data = [];
        if ($authUser->type != "0") {
            $data['companyArr'] = Company::whereIn('id', UserCompanyMap::where('user_id', $authUser->id)->pluck('company_id'))->where('is_active', 1)->orderBy('name')->get();
        } else {
            $data['companyArr'] = Company::where('is_active', 1)->orderBy('name')->get();
        }

        return view('app.votingreport.list', $data);
    }
    private function generatePDF($data)
    {
        // Set up Dompdf options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);

        // Initialize Dompdf
        $dompdf = new Dompdf($options);

        // Load the logo image
        $imagePath = public_path('homepage/assets/img/logo.png');
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $imageMimeType = mime_content_type($imagePath);
            $base64Image = "data:{$imageMimeType};base64,{$imageData}";
            $data['logo'] = $base64Image;
        } else {
            // Handle missing logo, set a default if necessary
            $data['logo'] = null;
        }

        // Load the HTML content into Dompdf
        $view = isset($data['view']) ? $data['view'] : 'app.votingreport.pdf';
        $html = view($view, $data)->render(); // Render the view to a string
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A3', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Return the Dompdf instance for further use
        return $dompdf;
    }
    private function generatereportPDF($data)
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $imagePath = public_path('homepage/assets/img/logo.png');
        $imageData = base64_encode(file_get_contents($imagePath));
        $imageMimeType = mime_content_type($imagePath);
        $base64Image = "data:{$imageMimeType};base64,{$imageData}";
        $data['logo'] = $base64Image;
        $dompdf->loadHtml(view('app.votingreport.reportpdf', $data));

        // (Optional) Set paper size and orientation
        $dompdf->setPaper('A3', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        return $dompdf;
    }

    public function get_report(Request $request)
    {
        try {
            $data = $detailsArr = [];
            $id = $request->id;
            $type = $request->type;
            $resolution = Resolution::find($id);

            $user = auth()->user();
            $logData['user_id'] = $user->id;
            $logData['resolution_id'] = $id;
            $logData['action'] = "Downloaded voting report (ID: {$id}).";
            addUserAction($logData);


            if ($type == 'pdf') {
                $data['resolution'] = $resolution;;

                $totalMembers  = $resolution->members;
                foreach ($resolution->resolution_details()->orderBy('index')->get() as $resolutionDetail) {
                    $votedMembers  = Member::whereIn('id', $resolutionDetail->votes->pluck('member_id'));
                    $absent_no_of_voters = Member::where('resolution_id', $id)->whereNotIn('id',  $resolutionDetail->votes->pluck('member_id'));

                    $assentMembers = Member::whereIn('id', $resolutionDetail->votes->where('resolution_choice', 'YES')->pluck('member_id'));
                    $dissentMembers = Member::whereIn('id', $resolutionDetail->votes->where('resolution_choice', 'No')->pluck('member_id'));
                    $abstainMembers = Member::whereIn('id', $resolutionDetail->votes->where('resolution_choice', 'ABSTAIN')->pluck('member_id'));

                    $total_voting_of_share = $totalMembers->sum('share');
                    $total_numbers_of_members = $totalMembers->count();

                    $total_voted_of_share = $votedMembers->sum('share');
                    $total_numbers_of_voters = $votedMembers->count();
                    $total_percentage_of_voted = ($total_voted_of_share / $total_voting_of_share) * 100;

                    $assent_voting_of_share =  $assentMembers->sum('share');
                    $assent_no_of_voters =  $assentMembers->count();

                    if ($resolution->user->user_type != 2) {
                        $assent_percentage =  $total_voted_of_share > 0 ? ($assent_voting_of_share * 100) / $total_voted_of_share : 0;
                    } else {
                        $assent_percentage =  $total_voting_of_share > 0 ? ($assent_voting_of_share / $total_voting_of_share) * 100 : 0;
                    }
                    $dissent_no_of_voters =  $dissentMembers->count();
                    $dissent_voting_of_share =  $dissentMembers->sum('share');
                    if ($resolution->user->user_type != 2) {
                        $dissent_percentage = $total_voted_of_share > 0 ? ($dissent_voting_of_share * 100) / $total_voted_of_share : 0;
                    } else {
                        $dissent_percentage = $total_voting_of_share > 0 ? ($dissent_voting_of_share /  $total_voting_of_share) * 100 : 0;
                    }

                    $abstain_voting_of_share =  $abstainMembers->sum('share');
                    $abstain_no_of_voters =  $abstainMembers->count();
                    if ($resolution->user->user_type != 2) {
                        $abstain_percentage = $total_voted_of_share > 0 ? ($abstain_voting_of_share * 100) / $total_voted_of_share : 0;
                    } else {
                        $abstain_percentage = $total_voting_of_share > 0 ? ($abstain_voting_of_share / $total_voting_of_share) * 100 : 0;
                    }

                    $absent_voting_of_share =  $absent_no_of_voters->sum('share');
                    $absent_no_of_voters = $absent_no_of_voters->count();
                    if ($resolution->user->user_type != 2) {
                        $absent_percentage = $total_voting_of_share > 0 ? ($absent_voting_of_share * 100) / $total_voting_of_share : 0;
                    } else {
                        $absent_percentage = $total_voting_of_share > 0 ? ($absent_voting_of_share / $total_voting_of_share) * 100 : 0;
                    }

                    array_push($detailsArr, [
                        'id' =>  $resolutionDetail->id,
                        'details' =>  $resolutionDetail->description,
                        'assent_no_of_voters' => $assent_no_of_voters,
                        'assent_voting_of_share' => $assent_voting_of_share,
                        'assent_percentage' => $assent_percentage,
                        'dissent_no_of_voters' => $dissent_no_of_voters,
                        'dissent_voting_of_share' => $dissent_voting_of_share,
                        'dissent_percentage' => $dissent_percentage,
                        'abstain_no_of_voters' => $abstain_no_of_voters,
                        'abstain_voting_of_share' => $abstain_voting_of_share,
                        'abstain_percentage' => $abstain_percentage,
                        'absent_no_of_voters' => $absent_no_of_voters,
                        'absent_voting_of_share' => $absent_voting_of_share,
                        'absent_percentage' => $absent_percentage,
                        'total_numbers_of_members' => $total_numbers_of_members,
                        'total_voting_of_share' => $total_voting_of_share,
                        'total_percentage_share' => 100,
                        'total_numbers_of_voters' => $total_numbers_of_voters,
                        'total_voted_of_share' => $total_voted_of_share,
                        'total_percentage_of_voted' => $total_percentage_of_voted,
                    ]);
                }
                $data['detailsArr'] = $detailsArr;
                // return view('app.votingreport.pdf', $data);
                // Generate the PDF
                $pdf = $this->generatePDF($data);

                // Output the PDF
                $fileName =  $resolution->company->name . '_finalvotingreport_' . $resolution->id . '.pdf';
                return $pdf->stream($fileName, ['Attachment' => true]);
            } elseif ($type == 'csv') {
                return Excel::download(new multisheetExport($id),  $resolution->company->name . '_finalvotingreport_' . $resolution->id . '.xlsx');
            } elseif ($type == 'reportpdf') {
                $data['resolution'] = $resolution;;

                $totalMembers  = $resolution->members;
                foreach ($resolution->resolution_details()->orderBy('index')->get() as $resolutionDetail) {
                    $votedMembers  = Member::whereIn('id', $resolutionDetail->votes->pluck('member_id'));
                    $absent_no_of_voters = Member::where('resolution_id', $id)->whereNotIn('id',  $resolutionDetail->votes->pluck('member_id'));

                    $assentMembers = Member::whereIn('id', $resolutionDetail->votes->where('resolution_choice', 'YES')->pluck('member_id'));
                    $dissentMembers = Member::whereIn('id', $resolutionDetail->votes->where('resolution_choice', 'No')->pluck('member_id'));
                    $abstainMembers = Member::whereIn('id', $resolutionDetail->votes->where('resolution_choice', 'ABSTAIN')->pluck('member_id'));

                    $total_voting_of_share = $totalMembers->sum('share');
                    $total_numbers_of_members = $totalMembers->count();

                    $total_voted_of_share = $votedMembers->sum('share');
                    $total_numbers_of_voters = $votedMembers->count();
                    $total_percentage_of_voted = ($total_voted_of_share / $total_voting_of_share) * 100;

                    $assent_voting_of_share =  $assentMembers->sum('share');
                    $assent_no_of_voters =  $assentMembers->count();

                    if ($resolution->user->user_type != 2) {
                        $assent_percentage =  $total_voted_of_share > 0 ? ($assent_voting_of_share * 100) / $total_voted_of_share : 0;
                    } else {
                        $assent_percentage =  $total_voting_of_share > 0 ? ($assent_voting_of_share / $total_voting_of_share) * 100 : 0;
                    }
                    $dissent_no_of_voters =  $dissentMembers->count();
                    $dissent_voting_of_share =  $dissentMembers->sum('share');
                    if ($resolution->user->user_type != 2) {
                        $dissent_percentage = $total_voted_of_share > 0 ? ($dissent_voting_of_share * 100) / $total_voted_of_share : 0;
                    } else {
                        $dissent_percentage = $total_voting_of_share > 0 ? ($dissent_voting_of_share /  $total_voting_of_share) * 100 : 0;
                    }

                    $abstain_voting_of_share =  $abstainMembers->sum('share');
                    $abstain_no_of_voters =  $abstainMembers->count();
                    if ($resolution->user->user_type != 2) {
                        $abstain_percentage = $total_voted_of_share > 0 ? ($abstain_voting_of_share * 100) / $total_voted_of_share : 0;
                    } else {
                        $abstain_percentage = $total_voting_of_share > 0 ? ($abstain_voting_of_share / $total_voting_of_share) * 100 : 0;
                    }

                    $absent_voting_of_share =  $absent_no_of_voters->sum('share');
                    $absent_no_of_voters = $absent_no_of_voters->count();
                    if ($resolution->user->user_type != 2) {
                        $absent_percentage = $total_voting_of_share > 0 ? ($absent_voting_of_share * 100) / $total_voting_of_share : 0;
                    } else {
                        $absent_percentage = $total_voting_of_share > 0 ? ($absent_voting_of_share / $total_voting_of_share) * 100 : 0;
                    }

                    array_push($detailsArr, [
                        'id' =>  $resolutionDetail->id,
                        'details' =>  $resolutionDetail->description,
                        'assent_no_of_voters' => $assent_no_of_voters,
                        'assent_voting_of_share' => $assent_voting_of_share,
                        'assent_percentage' => $assent_percentage,
                        'dissent_no_of_voters' => $dissent_no_of_voters,
                        'dissent_voting_of_share' => $dissent_voting_of_share,
                        'dissent_percentage' => $dissent_percentage,
                        'abstain_no_of_voters' => $abstain_no_of_voters,
                        'abstain_voting_of_share' => $abstain_voting_of_share,
                        'abstain_percentage' => $abstain_percentage,
                        'absent_no_of_voters' => $absent_no_of_voters,
                        'absent_voting_of_share' => $absent_voting_of_share,
                        'absent_percentage' => $absent_percentage,
                        'total_numbers_of_members' => $total_numbers_of_members,
                        'total_voting_of_share' => $total_voting_of_share,
                        'total_percentage_share' => 100,
                        'total_numbers_of_voters' => $total_numbers_of_voters,
                        'total_voted_of_share' => $total_voted_of_share,
                        'total_percentage_of_voted' => $total_percentage_of_voted,
                    ]);
                }

                $data['detailsArr'] = $detailsArr;
                // return view('app.votingreport.reportpdf', $data);
                // Generate the PDF
                $pdf = $this->generatereportPDF($data);

                // Output the PDF
                $fileName =  $resolution->company->name . '_finalvotingreport_' . $resolution->id . '.pdf';
                return $pdf->stream($fileName, ['Attachment' => true]);
            } else {
                return redirect()->back()->with('error', 'report not found.');
            }
        } catch (\Exception $e) {
            // Handle the exception and redirect back with an error message
            return redirect()->back()->with('error', 'something went wrong.');
        }
    }

    public function option_report(Request $request)
    {

        try {
            $data = $detailsArr = [];
            $id = $request->id;
            $type = $request->type;
            $resolution = Resolution::find($id);

            if ($type == 'pdf') {
                $data['resolution'] = $resolution;;
                $data['view'] = 'app.votingreport.option-pdf';
                $imagePath = public_path('homepage/assets/img/logo.png');
                $imageData = base64_encode(file_get_contents($imagePath));
                $imageMimeType = mime_content_type($imagePath);
                $base64Image = "data:{$imageMimeType};base64,{$imageData}";
                $data['logo'] = $base64Image;
                // Generate the PDF
                $pdf = $this->generatePDF($data);

                // Output the PDF
                $fileName =  $resolution->company->name . '_finalvotingreport_' . $resolution->id . '.pdf';
                return $pdf->stream($fileName, ['Attachment' => true]);
            } elseif ($type == 'csv') {
                return Excel::download(new multisheetExport($id),  $resolution->company->name . '_finalvotingreport_' . $resolution->id . '.xlsx');
            } elseif ($type == 'reportpdf') {
                $data['resolution'] = $resolution;;

                $totalMembers  = $resolution->members;
                foreach ($resolution->resolution_details as $resolutionDetail) {
                    $votedMembers  = Member::whereIn('id', $resolutionDetail->votes->pluck('member_id'));
                    $absent_no_of_voters = Member::where('resolution_id', $id)->whereNotIn('id',  $resolutionDetail->votes->pluck('member_id'));

                    $assentMembers = Member::whereIn('id', $resolutionDetail->votes->where('resolution_choice', 'YES')->pluck('member_id'));
                    $dissentMembers = Member::whereIn('id', $resolutionDetail->votes->where('resolution_choice', 'No')->pluck('member_id'));
                    $abstainMembers = Member::whereIn('id', $resolutionDetail->votes->where('resolution_choice', 'ABSTAIN')->pluck('member_id'));

                    $total_voting_of_share = $totalMembers->sum('share');
                    $total_numbers_of_members = $totalMembers->count();

                    $total_voted_of_share = $votedMembers->sum('share');
                    $total_numbers_of_voters = $votedMembers->count();
                    $total_percentage_of_voted = ($total_voted_of_share / $total_voting_of_share) * 100;

                    $assent_voting_of_share =  $assentMembers->sum('share');
                    $assent_no_of_voters =  $assentMembers->count();

                    if ($resolution->user->user_type != 2) {
                        $assent_percentage =  $total_voted_of_share > 0 ? ($assent_voting_of_share * 100) / $total_voted_of_share : 0;
                    } else {
                        $assent_percentage =  $total_voting_of_share > 0 ? ($assent_voting_of_share / $total_voting_of_share) * 100 : 0;
                    }
                    $dissent_no_of_voters =  $dissentMembers->count();
                    $dissent_voting_of_share =  $dissentMembers->sum('share');
                    if ($resolution->user->user_type != 2) {
                        $dissent_percentage = $total_voted_of_share > 0 ? ($dissent_voting_of_share * 100) / $total_voted_of_share : 0;
                    } else {
                        $dissent_percentage = $total_voting_of_share > 0 ? ($dissent_voting_of_share /  $total_voting_of_share) * 100 : 0;
                    }

                    $abstain_voting_of_share =  $abstainMembers->sum('share');
                    $abstain_no_of_voters =  $abstainMembers->count();
                    if ($resolution->user->user_type != 2) {
                        $abstain_percentage = $total_voted_of_share > 0 ? ($abstain_voting_of_share * 100) / $total_voted_of_share : 0;
                    } else {
                        $abstain_percentage = $total_voting_of_share > 0 ? ($abstain_voting_of_share / $total_voting_of_share) * 100 : 0;
                    }

                    $absent_voting_of_share =  $absent_no_of_voters->sum('share');
                    $absent_no_of_voters = $absent_no_of_voters->count();
                    if ($resolution->user->user_type != 2) {
                        $absent_percentage = $total_voting_of_share > 0 ? ($absent_voting_of_share * 100) / $total_voting_of_share : 0;
                    } else {
                        $absent_percentage = $total_voting_of_share > 0 ? ($absent_voting_of_share / $total_voting_of_share) * 100 : 0;
                    }

                    array_push($detailsArr, [
                        'id' =>  $resolutionDetail->id,
                        'details' =>  $resolutionDetail->description,
                        'assent_no_of_voters' => $assent_no_of_voters,
                        'assent_voting_of_share' => $assent_voting_of_share,
                        'assent_percentage' => $assent_percentage,
                        'dissent_no_of_voters' => $dissent_no_of_voters,
                        'dissent_voting_of_share' => $dissent_voting_of_share,
                        'dissent_percentage' => $dissent_percentage,
                        'abstain_no_of_voters' => $abstain_no_of_voters,
                        'abstain_voting_of_share' => $abstain_voting_of_share,
                        'abstain_percentage' => $abstain_percentage,
                        'absent_no_of_voters' => $absent_no_of_voters,
                        'absent_voting_of_share' => $absent_voting_of_share,
                        'absent_percentage' => $absent_percentage,
                        'total_numbers_of_members' => $total_numbers_of_members,
                        'total_voting_of_share' => $total_voting_of_share,
                        'total_percentage_share' => 100,
                        'total_numbers_of_voters' => $total_numbers_of_voters,
                        'total_voted_of_share' => $total_voted_of_share,
                        'total_percentage_of_voted' => $total_percentage_of_voted,
                    ]);
                }
                $data['detailsArr'] = $detailsArr;
                // return view('app.votingreport.reportpdf', $data);
                // Generate the PDF
                $pdf = $this->generatereportPDF($data);

                // Output the PDF
                $fileName =  $resolution->company->name . '_finalvotingreport_' . $resolution->id . '.pdf';
                return $pdf->stream($fileName, ['Attachment' => true]);
            } else {
                return redirect()->back()->with('error', 'report not found.');
            }
        } catch (\Exception $e) {
            // Handle the exception and redirect back with an error message
            return redirect()->back()->with('error', 'something went wrong.');
        }
    }
    public function new_report($id)
    {
        $resolution = Resolution::with('company', 'resolution_details', 'user')->find($id);

        if (!$resolution) {
            return redirect()->back()->with('error', 'Voting not found.');
        }
        $data['resolution'] = $resolution;
        $imagePath = public_path('homepage/assets/img/logo.png');
        $imageData = base64_encode(file_get_contents($imagePath));
        $imageMimeType = mime_content_type($imagePath);
        $base64Image = "data:{$imageMimeType};base64,{$imageData}";
        $data['logo'] = $base64Image;

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $imagePath = public_path('homepage/assets/img/logo.png');
        $imageData = base64_encode(file_get_contents($imagePath));
        $imageMimeType = mime_content_type($imagePath);
        $base64Image = "data:{$imageMimeType};base64,{$imageData}";
        $data['logo'] = $base64Image;
        $dompdf->loadHtml(view('app.votingreport.newreport', $data));

        // (Optional) Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        $fileName =  $resolution->company->name . '_finalvotingreport_' . $resolution->id . '.pdf';
        return $dompdf->stream($fileName, ['Attachment' => true]);


        // return view('app.votingreport.newreport', $data);
    }
    public function new_report_view($id)
    {
        $resolution = Resolution::with('company', 'resolution_details', 'user')->find($id);

        if (!$resolution) {
            return redirect()->back()->with('error', 'Voting not found.');
        }
        $data['resolution'] = $resolution;
        $imagePath = public_path('homepage/assets/img/logo.png');
        $imageData = base64_encode(file_get_contents($imagePath));
        $imageMimeType = mime_content_type($imagePath);
        $base64Image = "data:{$imageMimeType};base64,{$imageData}";
        $data['logo'] = $base64Image;

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $imagePath = public_path('homepage/assets/img/logo.png');
        $imageData = base64_encode(file_get_contents($imagePath));
        $imageMimeType = mime_content_type($imagePath);
        $base64Image = "data:{$imageMimeType};base64,{$imageData}";
        $data['logo'] = $base64Image;
        $dompdf->loadHtml(view('app.votingreport.newreport', $data));

        // (Optional) Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        $fileName =  $resolution->company->name . '_finalvotingreport_' . $resolution->id . '.pdf';
        // return $dompdf->stream($fileName, ['Attachment' => true]);


        return view('app.votingreport.newreport', $data);
    }
}
