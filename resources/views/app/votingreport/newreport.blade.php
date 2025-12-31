<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Voting Details</title>

    <style>
        @font-face {
            font-family: 'NotoSansDevanagari';
            src: url('{{ storage_path('fonts/NotoSansDevanagari-Regular.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: 'NotoSansDevanagari', sans-serif;
            font-size: 14px;
            line-height: 1.6;
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
            font-size: 15px;

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
            padding: 8px 12px;
            /*            text-align: center;*/
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
    </style>
</head>

<body>

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
                <table width="100%" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th rowspan="2">Sr. No.</th>
                            <th rowspan="2">Voter ID</th>
                            <th rowspan="2">Name of Voter</th>
                            <th colspan="4" width="20%">% of Voting</th>
                        </tr>
                        <tr>
                            <th>Agree (Accept)</th>
                            <th>Disagree (Reject)</th>
                            <th>Abstain</th>
                            <th width="10%">Not Voted</th>
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
                <table>
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Name of Voter / Email ID</th>
                            <!-- <th>Email ID</th> -->
                            <th>Date & Time of Voting</th>
                            <th>IP ADDRESS</th>
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

</body>

</html>
