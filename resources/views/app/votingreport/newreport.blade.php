<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Voting Details</title>

    @if(!isset($is_pdf) || !$is_pdf)
        <!-- Bootstrap / AdminLTE & Toastr Styles for Viewer -->
        <link rel="stylesheet" href="{{ asset('dist/css/adminlte.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
        <link href="{{ asset('customdownload/css/toastr.min.css') }}" rel="stylesheet">
        <style>
            body {
                padding-top: 80px !important;
                background-color: #f4f6f9 !important;
            }
            .content {
                box-shadow: 0 4px 15px rgba(0,0,0,0.08) !important;
                background-color: white !important;
                margin-top: 20px !important;
                margin-bottom: 40px !important;
                border-radius: 8px !important;
            }
            .header-bar {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                height: 60px;
                background: rgba(33, 37, 41, 0.95);
                backdrop-filter: blur(5px);
                z-index: 9999;
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0 30px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                color: white;
            }
            .header-title {
                font-size: 16px;
                font-weight: 600;
                margin: 0;
                color: #f8f9fa;
            }
            .action-btn {
                border-radius: 20px;
                padding: 6px 18px;
                font-weight: 500;
                font-size: 13px;
                transition: all 0.2s ease;
            }
        </style>
    @endif

    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
        }


        .header {
            padding: 0px;
            background-color: #f4f4f4;
            border-bottom: 1px solid #ddd;
        }

        .header-left p,
        .header-left span {
            margin: 0;
            font-size: 14px;

            padding: 2px 0;
            font-weight: bold;
        }

        .header-right {
            /* text-align: right; */
        }

        .header-right img {
            max-width: 120px;
            height: auto;
            margin-bottom: 5px;
        }


        .details {
            margin-bottom: 20px;
        }

        .details b {
            display: inline-block;
            width: 150px;
            font-weight: 600;
        }

        .divider {
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }

        .content {
            /* max-width: 900px; */
            margin: 0 auto;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px 10px;
            /*            text-align: center;*/
            word-wrap: break-word;
        }

        th {
            word-break: normal;
        }

        td {
            word-break: break-all;
        }

        .report-table {
            table-layout: fixed;
            width: 100%;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .total-row td {
            font-weight: bold;
            background-color: #e0e0e0;
        }

        /* Force new page for each resolution detail except the first one */
        .page-break {
            page-break-before: always;
        }

        /* Prevent table rows and table headers from splitting across page breaks */
        thead {
            display: table-header-group;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }
    </style>
</head>

<body>

    @if(!isset($is_pdf) || !$is_pdf)
        <div class="header-bar">
            <h5 class="header-title">
                <i class="fas fa-file-alt mr-2 text-info"></i> Voting Report Viewer
            </h5>
            <div>
                <a href="{{ route('votingreport.index') }}" class="btn btn-outline-light action-btn mr-2">
                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                </a>
                <button id="download-report-btn" class="btn btn-info action-btn text-white">
                    <i class="fas fa-cloud-download-alt mr-1"></i> Download PDF
                </button>
            </div>
        </div>
    @endif

    <div class="content">

        @php
            $resCount = $resolution->resolution_details->count();
        @endphp

        @foreach ($resolution->resolution_details as $resolutionDetail)
            @if (!$loop->first)
                <!-- Add page break for all but the first detail -->
                <div class="page-break"></div>
            @endif

            <div class="header">

                <table width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="header-left" style="border: 0px solid #000;">
                            <p>{{ isset($resolution->company) ? $resolution->company->name : 'N/A' }}</p>
                            <p>{{ $resolution->meeting_details }}</p>
                            <p>Name of Person: {{ isset($resolution->user) ? $resolution->user->name : 'N/A' }}</p>
                            <span>Voting Start:
                                {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $resolution->start_date)->format('d-M-Y H:i:s') }}
                            </span><br>
                            <span>Voting End:
                                {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $resolution->end_date)->format('d-M-Y H:i:s') }}
                            </span><br>
                            <span>Voting No. {{ $resolution->id }}</span>
                        </td>
                        <td class="header-right" align="right" style="border: 0px solid #000;">
                            <img src="{{ $logo }}" alt="Admin Logo"><br>
                            <p>FINAL REPORT</p>
                            <p>Voting No {{ $loop->index + 1 }} of {{ $resCount }}</p>
                        </td>
                    </tr>
                </table>
            </div>


            <div class="content">
                <h3>Voting Details {{ $loop->index + 1 }}</h3>
                <p>{!! nl2br(e($resolutionDetail->description)) !!}</p>
                <table class="report-table" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th rowspan="2" width="6%">Sr. No.</th>
                            <th rowspan="2" width="20%">Voter ID</th>
                            <th rowspan="2" width="24%">Name of Voter</th>
                            <th colspan="4" width="50%">% of Voting</th>
                        </tr>
                        <tr>
                            <th width="12%">Agree (Accept)</th>
                            <th width="13%">Disagree (Reject)</th>
                            <th width="12%">Abstain</th>
                            <th width="13%">Not Voted</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalYes = 0;
                            $totalNo = 0;
                            $totalAbstain = 0;
                            $totalUnvoted = 0;
                        @endphp

                        @foreach ($resolution->members as $member)
                            @php
                                $vote = $member->res_vote
                                    ->where('resolution_details_id', $resolutionDetail->id)
                                    ->first();

                                $yesShare = $vote && $vote->resolution_choice === 'YES' ? $member->share : 0;
                                $noShare = $vote && $vote->resolution_choice === 'No' ? $member->share : 0;
                                $abstainShare = $vote && $vote->resolution_choice === 'ABSTAIN' ? $member->share : 0;
                                $unvotedShare = isset($member->vote) ? 0 : $member->share;

                                $totalYes += $yesShare;
                                $totalNo += $noShare;
                                $totalAbstain += $abstainShare;
                                $totalUnvoted += $unvotedShare;
                            @endphp

                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $member->user_name }}</td>
                                <td>{{ $member->name }}</td>
                                <td>{{ $yesShare }}</td>
                                <td>{{ $noShare }}</td>
                                <td>{{ $abstainShare }}</td>
                                <td>{{ $unvotedShare }}</td>
                            </tr>
                        @endforeach

                        <tr class="total-row">
                            <td colspan="3">TOTAL</td>
                            <td>{{ $totalYes }}</td>
                            <td>{{ $totalNo }}</td>
                            <td>{{ $totalAbstain }}</td>
                            <td>{{ $totalUnvoted }}</td>
                        </tr>

                    </tbody>
                </table>
                <br>
                <table class="report-table">
                    <thead>
                        <tr>
                            <th width="8%">Sr No.</th>
                            <th width="42%">Name of Voter / Email ID</th>
                            <!-- <th>Email ID</th> -->
                            <th width="20%">Date & Time of Voting</th>
                            <th width="30%">IP ADDRESS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($resolution->members as $member)
                            @php
                                $vote = $member->res_vote
                                    ->where('resolution_details_id', $resolutionDetail->id)
                                    ->first();
                            @endphp
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $member->name }} <br> {{ $member->email }}</td>
                                <!-- <td></td> -->
                                <td>{{ isset($member->vote) ? $member->vote->created_at : '-' }}</td>
                                <td>{{ isset($member->vote->ipaddress) ? $member->vote->ipaddress : '' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>

    @if(!isset($is_pdf) || !$is_pdf)
        <!-- jQuery, Bootstrap & Toastr JS -->
        <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('customdownload/js/toastr.js') }}"></script>
        <script>
            $(document).ready(function() {
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-top-center",
                    "timeOut": "5000"
                };

                $('#download-report-btn').click(function(e) {
                    e.preventDefault();
                    var btn = $(this);
                    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Queuing...');

                    $.ajax({
                        url: "{{ route('votingreport.request_download') }}",
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            resolution_id: "{{ $resolution->id }}",
                            format: "new_report"
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success(response.message);
                            } else {
                                toastr.error("Failed to request download.");
                            }
                        },
                        error: function(xhr) {
                            var msg = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : "Failed to queue download.";
                            toastr.error(msg);
                        },
                        complete: function() {
                            btn.prop('disabled', false).html('<i class="fas fa-cloud-download-alt mr-1"></i> Download PDF');
                        }
                    });
                });
            });
        </script>
    @endif

</body>

</html>
