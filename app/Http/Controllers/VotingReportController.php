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
use App\Models\ReportDownload;
use App\Jobs\GenerateReportJob;
use Illuminate\Support\Facades\Storage;

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
                    $btn = '<button type="button" class="btn btn-primary btn-sm request-download-btn" data-id="' . $row->id . '" data-format="excel"><i class="fas fa-file-excel mr-1"></i>Excel</button>';
                    return $btn;
                })
                ->addColumn('new_report', function ($row) {
                    if ($row->evsn_type == '2') {
                        $btn = '';
                    } else {
                        $btn = '<button type="button" class="btn btn-success btn-sm request-download-btn" data-id="' . $row->id . '" data-format="new_report"><i class="fas fa-file-pdf mr-1"></i>PDF</button>';
                    }
                    return $btn;
                })
                ->addColumn('new_report_view', function ($row) {
                    if ($row->evsn_type == '2') {
                        $btn = '';
                    } else {
                        $route = route('votingreport.new_report_view', ['id' => $row->id]);
                        $btn = ' <a href="' . $route . '"  class="btn btn-info btn-sm" ><i class="fas fa-eye mr-1"></i>View</a>';
                    }
                    return $btn;
                })
                ->addColumn('pdf', function ($row) {
                    $btn = '<button type="button" class="btn btn-success btn-sm request-download-btn" data-id="' . $row->id . '" data-format="pdf"><i class="fas fa-file-pdf mr-1"></i>PDF</button>';
                    return $btn;
                })
                ->addColumn('reportpdf', function ($row) {
                    $btn = '<button type="button" class="btn btn-success btn-sm request-download-btn" data-id="' . $row->id . '" data-format="reportpdf"><i class="fas fa-percent mr-1"></i>Report</button>';
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
            $authUser = auth()->user();
            if ($authUser->type == '0') {
                $resolution = Resolution::find($id);
            } else {
                $resolution = Resolution::where('user_id',$authUser->id)->where('id', $id)->first();
            }
            if (!$resolution) {
                return redirect()->route('votingreport.index')->with('error', 'Voting not found.');
            }

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
            $authUser = auth()->user();
            if ($authUser->type == '0') {
                $resolution = Resolution::find($id);
            } else {
                $resolution = Resolution::where('user_id',$authUser->id)->where('id', $id)->first();
            }
            if (!$resolution) {
                return redirect()->route('votingreport.index')->with('error', 'Voting not found.');
            }

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
        $authUser = auth()->user();
        if ($authUser->type == '0') {
            $resolution = Resolution::with('company', 'resolution_details', 'user')->find($id);
        } else {
            $resolution = Resolution::with('company', 'resolution_details', 'user')->where('user_id',$authUser->id)->where('id', $id)->first();
        }
        if (!$resolution) {
            return redirect()->route('votingreport.index')->with('error', 'Voting not found.');
        }
        $data['resolution'] = $resolution;
        $data['is_pdf'] = true;
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
        $authUser = auth()->user();
        if ($authUser->type == '0') {
            $resolution = Resolution::with('company', 'resolution_details', 'user')->find($id);
        } else {
            $resolution = Resolution::with('company', 'resolution_details', 'user')->where('user_id',$authUser->id)->where('id', $id)->first();
        }
        if (!isset($resolution)) {
            return redirect()->route('votingreport.index')->with('error', 'Voting not found.');
        }

        $data['resolution'] = $resolution;
        $data['is_pdf'] = false;
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

    /**
     * Request a report download in the background.
     */
    public function requestDownload(Request $request)
    {
        $request->validate([
            'resolution_id' => 'required|exists:resolutions,id',
            'format' => 'required|in:excel,pdf,reportpdf,new_report'
        ]);

        $id = $request->resolution_id;
        $format = $request->format;
        $authUser = auth()->user();

        // Access Control
        if ($authUser->type == '0') {
            $resolution = Resolution::with('company')->find($id);
        } else {
            $resolution = Resolution::with('company')->where('user_id', $authUser->id)->where('id', $id)->first();
        }

        if (!$resolution) {
            return response()->json(['error' => 'Voting report not found or unauthorized.'], 403);
        }

        // Format filename: replace spaces/special chars with underscores
        $companyName = preg_replace('/[^A-Za-z0-9\-]/', '_', $resolution->company->name);
        $ext = $format === 'excel' ? 'xlsx' : 'pdf';
        $reportName = $companyName . '_' . $format . 'report_' . $resolution->id . '_' . time() . '.' . $ext;

        // Log action
        $logData = [
            'user_id' => $authUser->id,
            'resolution_id' => $id,
            'action' => "Requested async report download: ID {$id} (format: {$format})."
        ];
        addUserAction($logData);

        // Create download record
        $downloadRequest = ReportDownload::create([
            'user_id' => $authUser->id,
            'resolution_id' => $id,
            'report_type' => $format,
            'report_name' => $reportName,
            'status' => 'queued',
            'progress' => 0
        ]);

        // Dispatch background queue job
        dispatch(new GenerateReportJob($downloadRequest));

        return response()->json([
            'success' => true,
            'message' => 'Your download request has been received. The report is being generated in the background. You can continue using the application and check the Downloads section for its status.',
            'download' => $downloadRequest
        ]);
    }

    /**
     * Get all report downloads for the current user.
     */
    public function getDownloads()
    {
        $authUser = auth()->user();
        $downloads = ReportDownload::with('resolution.company')
            ->where('user_id', $authUser->id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'downloads' => $downloads->map(function($download) {
                return [
                    'id' => $download->id,
                    'report_name' => $download->report_name,
                    'report_type' => strtoupper($download->report_type),
                    'company_name' => $download->resolution->company->name ?? 'N/A',
                    'resolution_id' => $download->resolution_id,
                    'requested_at' => $download->created_at->format('d-M-Y g:i A'),
                    'status' => ucfirst($download->status),
                    'progress' => $download->progress,
                    'download_url' => route('votingreport.download_file', ['id' => $download->id]),
                    'error_message' => $download->error_message ? strtok($download->error_message, "\n") : null,
                ];
            })
        ]);
    }

    /**
     * Download the completed report file securely.
     */
    public function downloadFile($id)
    {
        $authUser = auth()->user();
        $download = ReportDownload::find($id);

        if (!$download) {
            abort(404, 'Download record not found.');
        }

        if ($download->user_id !== $authUser->id && $authUser->type !== '0') {
            abort(403, 'Unauthorized access.');
        }

        if ($download->status !== 'completed' || !$download->file_path || !Storage::disk('public')->exists($download->file_path)) {
            abort(404, 'File not ready or does not exist.');
        }

        $fullPath = Storage::disk('public')->path($download->file_path);
        $response = response()->download($fullPath, $download->report_name)->deleteFileAfterSend(true);
        $download->delete();
        return $response;
    }

    /**
     * Retry a failed report download.
     */
    public function retryDownload($id)
    {
        $authUser = auth()->user();
        $download = ReportDownload::find($id);

        if (!$download) {
            return response()->json(['error' => 'Download record not found.'], 404);
        }

        if ($download->user_id !== $authUser->id && $authUser->type !== '0') {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $download->update([
            'status' => 'queued',
            'progress' => 0,
            'error_message' => null
        ]);

        dispatch(new GenerateReportJob($download));

        return response()->json([
            'success' => true,
            'message' => 'The report is being regenerated in the background. You can check the Downloads section for its status.',
            'download' => $download
        ]);
    }
}
