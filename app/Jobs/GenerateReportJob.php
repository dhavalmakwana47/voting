<?php

namespace App\Jobs;

use App\Exports\multisheetExport;
use App\Models\Member;
use App\Models\ReportDownload;
use App\Models\Resolution;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Exception;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 900;

    protected $downloadRequest;

    /**
     * Create a new job instance.
     */
    public function __construct(ReportDownload $downloadRequest)
    {
        $this->downloadRequest = $downloadRequest;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Prevent PHP memory and execution timeouts for large reports
        ini_set('memory_limit', '1024M');
        set_time_limit(900);

        $request = $this->downloadRequest;

        try {
            // Update status to processing and progress to 10%
            $request->update([
                'status' => 'processing',
                'progress' => 10
            ]);

            $id = $request->resolution_id;
            $format = $request->report_type;

            $resolution = Resolution::with('company', 'resolution_details', 'user')->find($id);
            if (!$resolution) {
                throw new Exception("Resolution with ID {$id} not found.");
            }

            $request->update(['progress' => 30]);

            $fileName = $request->report_name;
            $relativeDir = 'downloads/' . $request->user_id;
            $filePath = $relativeDir . '/' . $fileName;

            // Ensure directory exists in public disk
            if (!Storage::disk('public')->exists($relativeDir)) {
                Storage::disk('public')->makeDirectory($relativeDir);
            }

            if ($format === 'excel') {
                // Generate Excel using multisheetExport
                $request->update(['progress' => 50]);
                Excel::store(new multisheetExport($id), $filePath, 'public');
            } else {
                // PDF reports
                $data = [];
                $request->update(['progress' => 45]);

                if ($format === 'pdf' || $format === 'reportpdf') {
                    $detailsArr = [];
                    $totalMembers = $resolution->members;

                    foreach ($resolution->resolution_details()->orderBy('index')->get() as $resolutionDetail) {
                        $votedMembers = Member::whereIn('id', $resolutionDetail->votes->pluck('member_id'));
                        $absent_no_of_voters = Member::where('resolution_id', $id)->whereNotIn('id', $resolutionDetail->votes->pluck('member_id'));

                        $assentMembers = Member::whereIn('id', $resolutionDetail->votes->where('resolution_choice', 'YES')->pluck('member_id'));
                        $dissentMembers = Member::whereIn('id', $resolutionDetail->votes->where('resolution_choice', 'No')->pluck('member_id'));
                        $abstainMembers = Member::whereIn('id', $resolutionDetail->votes->where('resolution_choice', 'ABSTAIN')->pluck('member_id'));

                        $total_voting_of_share = $totalMembers->sum('share');
                        $total_numbers_of_members = $totalMembers->count();

                        $total_voted_of_share = $votedMembers->sum('share');
                        $total_numbers_of_voters = $votedMembers->count();
                        $total_percentage_of_voted = $total_voting_of_share > 0 ? ($total_voted_of_share / $total_voting_of_share) * 100 : 0;

                        $assent_voting_of_share = $assentMembers->sum('share');
                        $assent_no_of_voters = $assentMembers->count();

                        if ($resolution->user->user_type != 2) {
                            $assent_percentage = $total_voted_of_share > 0 ? ($assent_voting_of_share * 100) / $total_voted_of_share : 0;
                        } else {
                            $assent_percentage = $total_voting_of_share > 0 ? ($assent_voting_of_share / $total_voting_of_share) * 100 : 0;
                        }

                        $dissent_no_of_voters = $dissentMembers->count();
                        $dissent_voting_of_share = $dissentMembers->sum('share');
                        if ($resolution->user->user_type != 2) {
                            $dissent_percentage = $total_voted_of_share > 0 ? ($dissent_voting_of_share * 100) / $total_voted_of_share : 0;
                        } else {
                            $dissent_percentage = $total_voting_of_share > 0 ? ($dissent_voting_of_share / $total_voting_of_share) * 100 : 0;
                        }

                        $abstain_voting_of_share = $abstainMembers->sum('share');
                        $abstain_no_of_voters = $abstainMembers->count();
                        if ($resolution->user->user_type != 2) {
                            $abstain_percentage = $total_voted_of_share > 0 ? ($abstain_voting_of_share * 100) / $total_voted_of_share : 0;
                        } else {
                            $abstain_percentage = $total_voting_of_share > 0 ? ($abstain_voting_of_share / $total_voting_of_share) * 100 : 0;
                        }

                        $absent_voting_of_share = $absent_no_of_voters->sum('share');
                        $absent_no_of_voters = $absent_no_of_voters->count();
                        if ($resolution->user->user_type != 2) {
                            $absent_percentage = $total_voting_of_share > 0 ? ($absent_voting_of_share * 100) / $total_voting_of_share : 0;
                        } else {
                            $absent_percentage = $total_voting_of_share > 0 ? ($absent_voting_of_share / $total_voting_of_share) * 100 : 0;
                        }

                        $detailsArr[] = [
                            'id' => $resolutionDetail->id,
                            'details' => $resolutionDetail->description,
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
                        ];
                    }

                    $data['resolution'] = $resolution;
                    $data['detailsArr'] = $detailsArr;

                    $request->update(['progress' => 60]);

                    if ($format === 'pdf') {
                        if ($resolution->evsn_type == '2') {
                            $data['view'] = 'app.votingreport.option-pdf';
                        } else {
                            $data['view'] = 'app.votingreport.pdf';
                        }
                        $dompdf = $this->generatePDF($data);
                    } else { // reportpdf
                        $dompdf = $this->generatereportPDF($data);
                    }
                } elseif ($format === 'new_report') {
                    $data['resolution'] = $resolution;
                    $data['is_pdf'] = true;
                    $request->update(['progress' => 60]);
                    $dompdf = $this->generateNewReportPDF($data);
                } else {
                    throw new Exception("Unsupported report format: {$format}");
                }

                $request->update(['progress' => 80]);
                $pdfContent = $dompdf->output();
                Storage::disk('public')->put($filePath, $pdfContent);
            }

            $request->update([
                'status' => 'completed',
                'progress' => 100,
                'file_path' => $filePath,
                'error_message' => null,
            ]);

        } catch (\Throwable $e) {
            $request->update([
                'status' => 'failed',
                'progress' => 0,
                'error_message' => $e->getMessage() . "\n" . $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function generatePDF($data)
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        $imagePath = public_path('homepage/assets/img/logo.png');
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $imageMimeType = mime_content_type($imagePath);
            $base64Image = "data:{$imageMimeType};base64,{$imageData}";
            $data['logo'] = $base64Image;
        } else {
            $data['logo'] = null;
        }

        $view = isset($data['view']) ? $data['view'] : 'app.votingreport.pdf';
        $html = view($view, $data)->render();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A3', 'landscape');
        $dompdf->render();

        return $dompdf;
    }

    private function generatereportPDF($data)
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        $imagePath = public_path('homepage/assets/img/logo.png');
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $imageMimeType = mime_content_type($imagePath);
            $base64Image = "data:{$imageMimeType};base64,{$imageData}";
            $data['logo'] = $base64Image;
        } else {
            $data['logo'] = null;
        }

        $dompdf->loadHtml(view('app.votingreport.reportpdf', $data)->render());
        $dompdf->setPaper('A3', 'landscape');
        $dompdf->render();

        return $dompdf;
    }

    private function generateNewReportPDF($data)
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        $imagePath = public_path('homepage/assets/img/logo.png');
        if (file_exists($imagePath)) {
            $imageData = base64_encode(file_get_contents($imagePath));
            $imageMimeType = mime_content_type($imagePath);
            $base64Image = "data:{$imageMimeType};base64,{$imageData}";
            $data['logo'] = $base64Image;
        } else {
            $data['logo'] = null;
        }

        $dompdf->loadHtml(view('app.votingreport.newreport', $data)->render());
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        if ($this->downloadRequest) {
            $this->downloadRequest->update([
                'status' => 'failed',
                'progress' => 0,
                'error_message' => $exception->getMessage()
            ]);
        }
    }
}
