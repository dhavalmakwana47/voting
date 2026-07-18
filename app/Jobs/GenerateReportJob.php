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

            $resolution = Resolution::with(['company', 'resolution_details', 'user', 'votes', 'members.res_vote', 'members.vote'])->find($id);
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
                // PDF reports — partitioned by resolution detail + member chunking
                $resolutionDetails = $resolution->resolution_details()->orderBy('index')->get();
                $allMembers = $resolution->members;
                $memberChunkSize = 500; // Max members per single PDF to avoid Dompdf timeout
                $pdfFiles = [];

                $request->update(['progress' => 40]);

                $totalDetailsCount = $resolutionDetails->count();
                if ($totalDetailsCount == 0) { $totalDetailsCount = 1; }
                $totalMembersCount = $allMembers->count();

                // We need a ZIP if there are multiple details OR members exceed chunk size
                // OR if is_zip flag is set on the resolution (set after a previous failure)
                $needsZip = $resolution->is_zip;

                // Helper: build detailsArr for a given collection of resolution details
                $buildDetailsArr = function ($details) use ($resolution, $allMembers, $id) {
                    $detailsArr = [];
                    foreach ($details as $resolutionDetail) {
                        $votedMembers = Member::whereIn('id', $resolutionDetail->votes->pluck('member_id'));
                        $absent_no_of_voters_q = Member::where('resolution_id', $id)->whereNotIn('id', $resolutionDetail->votes->pluck('member_id'));

                        $assentMembers = Member::whereIn('id', $resolutionDetail->votes->where('resolution_choice', 'YES')->pluck('member_id'));
                        $dissentMembers = Member::whereIn('id', $resolutionDetail->votes->where('resolution_choice', 'No')->pluck('member_id'));
                        $abstainMembers = Member::whereIn('id', $resolutionDetail->votes->where('resolution_choice', 'ABSTAIN')->pluck('member_id'));

                        $total_voting_of_share = $allMembers->sum('share');
                        $total_numbers_of_members = $allMembers->count();

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

                        $absent_voting_of_share = $absent_no_of_voters_q->sum('share');
                        $absent_no_of_voters = $absent_no_of_voters_q->count();
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
                    return $detailsArr;
                };

                if (!$needsZip) {
                    // Small report: single detail + few members — one PDF
                    $data = [];
                    $data['resolution'] = $resolution;
                    $data['totalMembersCount'] = $totalMembersCount;
                    $data['totalResolutionDetailsCount'] = $totalDetailsCount;
                    if ($format === 'pdf' || $format === 'reportpdf') {
                        $data['detailsArr'] = $buildDetailsArr($resolutionDetails);
                        $request->update(['progress' => 60]);
                        if ($format === 'pdf') {
                            $data['view'] = $resolution->evsn_type == '2' ? 'app.votingreport.option-pdf' : 'app.votingreport.pdf';
                            $dompdf = $this->generatePDF($data);
                        } else {
                            $dompdf = $this->generatereportPDF($data);
                        }
                    } elseif ($format === 'new_report') {
                        $data['is_pdf'] = true;
                        $request->update(['progress' => 60]);
                        $dompdf = $this->generateNewReportPDF($data);
                    } else {
                        throw new Exception("Unsupported report format: {$format}");
                    }
                    $request->update(['progress' => 80]);
                    Storage::disk('public')->put($filePath, $dompdf->output());
                } else {
                    // Large report: split by detail AND chunk members within each detail
                    // Pre-calculate total parts for progress tracking
                    $totalParts = 0;
                    foreach ($resolutionDetails as $detail) {
                        $totalParts += max(1, intval(ceil($totalMembersCount / $memberChunkSize)));
                    }
                    $partCounter = 0;

                    foreach ($resolutionDetails as $resolutionDetail) {
                        $detailsArr = $buildDetailsArr(collect([$resolutionDetail]));
                        $memberChunks = $allMembers->chunk($memberChunkSize);

                        foreach ($memberChunks as $chunkIndex => $chunkMembers) {
                            $partCounter++;
                            $progressPercent = 40 + intval(($partCounter / $totalParts) * 40);
                            $request->update(['progress' => min($progressPercent, 80)]);

                            // Clone resolution, set this detail only + chunked members
                            $chunkResolution = clone $resolution;
                            $chunkResolution->setRelation('resolution_details', collect([$resolutionDetail]));
                            $chunkResolution->setRelation('members', $chunkMembers);

                            $chunkData = [];
                            $chunkData['resolution'] = $chunkResolution;
                            $chunkData['detailsArr'] = $detailsArr;
                            $chunkData['totalMembersCount'] = $totalMembersCount;
                            $chunkData['totalResolutionDetailsCount'] = $totalDetailsCount;

                            if ($format === 'pdf' || $format === 'reportpdf') {
                                if ($format === 'pdf') {
                                    $chunkData['view'] = $resolution->evsn_type == '2' ? 'app.votingreport.option-pdf' : 'app.votingreport.pdf';
                                    $dompdf = $this->generatePDF($chunkData);
                                } else {
                                    $dompdf = $this->generatereportPDF($chunkData);
                                }
                            } elseif ($format === 'new_report') {
                                $chunkData['is_pdf'] = true;
                                $dompdf = $this->generateNewReportPDF($chunkData);
                            }

                            $partName = 'item' . $resolutionDetail->id . '_part' . ($chunkIndex + 1);
                            $partFileName = str_replace('.pdf', '_' . $partName . '.pdf', $fileName);
                            $partFilePath = $relativeDir . '/' . $partFileName;
                            Storage::disk('public')->put($partFilePath, $dompdf->output());
                            $pdfFiles[] = Storage::disk('public')->path($partFilePath);

                            unset($dompdf); // Free memory
                        }
                    }

                    // Create ZIP archive
                    $zipFileName = str_replace('.pdf', '.zip', $fileName);
                    $zipFilePath = $relativeDir . '/' . $zipFileName;
                    $zipPhysicalPath = Storage::disk('public')->path($zipFilePath);

                    $zip = new \ZipArchive();
                    if ($zip->open($zipPhysicalPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                        foreach ($pdfFiles as $pdfFile) {
                            $zip->addFile($pdfFile, basename($pdfFile));
                        }
                        $zip->close();
                    }

                    // Clean up temporary PDFs
                    foreach ($pdfFiles as $pdfFile) {
                        @unlink($pdfFile);
                    }

                    $filePath = $zipFilePath;
                    $fileName = $zipFileName;
                }
            }

            $request->update([
                'status' => 'completed',
                'progress' => 100,
                'file_path' => $filePath,
                'report_name' => $fileName,
                'error_message' => null,
            ]);
        } catch (\Throwable $e) {
            $request->update([
                'status' => 'failed',
                'progress' => 0,
                'error_message' => $e->getMessage() . "\n" . $e->getTraceAsString()
            ]);

            // Set is_zip flag on the resolution so retries always download as ZIP
            $resolutionModel = Resolution::find($request->resolution_id);
            if ($resolutionModel) {
                $resolutionModel->update(['is_zip' => true]);
            }

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

            // Set is_zip flag so retries always download as ZIP
            $resolution = Resolution::find($this->downloadRequest->resolution_id);
            if ($resolution) {
                $resolution->update(['is_zip' => true]);
            }
        }
    }
}
