<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Summary Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
            /* Light background for contrast */
            color: #333;
            /* Darker text for better readability */
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
            /* Add spacing between tables */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            /* Add shadow for depth */
            background-color: #ffffff;
            /* White background for tables */
        }

        th,
        td {
            border: 1px solid #dddddd;
            /* Light border for a softer look */
            padding: 12px;
            /* Increased padding for better spacing */
            text-align: left;
            /* Left-align text */
            transition: background-color 0.3s;
            /* Smooth hover transition */
        }

        th {
            background-color: #007bff;
            /* Bootstrap primary color */
            color: white;
            /* White text for headers */
        }

        th:hover {
            background-color: #0056b3;
            /* Darker shade on hover */
        }

        h3 {
            margin: 10px 0;
            /* Space above and below the heading */
        }

        .center {
            text-align: center;
            /* Center-align text */
        }

        .table-title {
            font-size: 20px;
            /* Increased font size for the title */
            font-weight: bold;
            /* Bold the title */
            margin: 20px 0;
            /* Space above and below the title */
            text-align: center;
            /* Center-align the title */
        }

        .bordered-table {
            border: 1px solid #dddddd;
            /* Outer border for the table */
            padding: 10px;
            border-radius: 5px;
            /* Rounded corners for a modern look */
            overflow: hidden;
            /* Ensures child elements respect border radius */
            background-color: #ffffff;
            /* White background for the outer div */
        }

        @media (max-width: 768px) {

            table,
            th,
            td {
                font-size: 12px;
                /* Smaller font size on mobile devices */
                padding: 8px;
                /* Reduced padding on mobile */
            }
        }
    </style>
</head>

<body>
    <div class="bordered-table">
        <table>
            <tr>
                <td colspan="2" class="center">
                    <img src="{{ $logo }}" alt="Admin Logo"
                        style="width: 150px; height: auto; display: block; margin: 0 auto;">
                    <h3>Final Report</h3>
                </td>
            </tr>
            <tr>
                <td>Report Download Date and Time:</td>
                <td colspan="4">{{ Carbon\Carbon::now()->format('d-M-Y H:i:s') }}</td>
            </tr>
            <tr>
                <td>Company Name</td>
                <td>Name of Person</td>
                <td>Voting No.</td>
                <td>Voted Voter</td>
                <td>Total Voter</td>
            </tr>
            <tr>
                <td>{{ $resolution->company->name }}</td>
                <td>{{ $resolution->user->name }}</td>
                <td>{{ $resolution->id }}</td>
                <td>{{ $resolution->option_votes()->distinct('member_id')->count() }}</td>
                <td>{{ $resolution->members->count() }}</td>
            </tr>
            <tr>
                <td>Voting Start / End Date and Time:</td>
                <td colspan="4">
                    {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $resolution->start_date)->format('d-M-Y H:i:s') }}
                    -
                    {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $resolution->end_date)->format('d-M-Y H:i:s') }}
                </td>
            </tr>
        </table>

        @php
            $totalMembers = $resolution->members;
            $total_voting_of_share = $totalMembers->sum('share');
            $total_numbers_of_members = $totalMembers->count();
        @endphp
        <table border="1" cellpadding="6" cellspacing="0"
            style="width: 100%; border-collapse: collapse; font-family: 'DejaVu Sans', sans-serif;">
            <thead style="background-color: #343a40; color: white;">
                <tr>
                    <th>Item Number</th>
                    <th>Resolution</th>
                    <th colspan="4">Voted Voter</th>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td><b>Option</b></td>
                    <td><b>No of Voters</b></td>
                    <td><b>Voting of share</b></td>
                    <td><b>% of Voter</b></td>
                </tr>
            </thead>
            <tbody>
                @foreach ($resolution->resolution_details as $resolutionDetail)
                    @php
                        $labels = $resolutionDetail->labels;
                        $maxShare = $labels
                            ->map(function ($label) use ($resolution) {
                                return $resolution
                                    ->members()
                                    ->whereIn('id', $label->option_votes->pluck('member_id')->toArray())
                                    ->sum('share');
                            })
                            ->max();

                        $votedMembers = App\Models\Member::whereIn(
                            'id',
                            $resolutionDetail->option_votes->pluck('member_id'),
                        );
                        $totalVotedShare = $votedMembers->sum('share');
                        $totalVoters = $votedMembers->count();
                        $votedPercentage =
                            $total_voting_of_share > 0
                                ? number_format(($totalVotedShare / $total_voting_of_share) * 100, 2)
                                : 0;
                    @endphp

                    @foreach ($labels as $index => $label)
                        @php
                            $currentVotedShare = $resolution
                                ->members()
                                ->whereIn('id', $label->option_votes->pluck('member_id')->toArray())
                                ->sum('share');
                            $isMax = $currentVotedShare == $maxShare;
                            if ($resolution->user->user_type != 2) {
                                $currentPercentage =
                                    $totalVotedShare > 0
                                        ? number_format(($currentVotedShare * 100) / $totalVotedShare, 2)
                                        : 0;
                            } else {
                                $currentPercentage =
                                    $total_voting_of_share > 0
                                        ? number_format(($currentVotedShare / $total_voting_of_share) * 100, 2)
                                        : 0;
                            }
                            $highlightStyle = $isMax ? 'background-color: #cce5ff;' : '';
                        @endphp

                        <tr>
                            <td>{{ $resolutionDetail->id }}</td>
                            <td style="vertical-align: top; padding-top: 8px;">
                                {!! nl2br(e($resolutionDetail->description)) !!}
                            </td>
                            <td style="{{ $highlightStyle }}">{{ $label->label }}</td>
                            <td style="{{ $highlightStyle }}">{{ $label->option_votes->count() }}</td>
                            <td style="{{ $highlightStyle }}">{{ $currentVotedShare }}</td>
                            <td style="{{ $highlightStyle }}">{{ $currentPercentage }}</td>
                        </tr>
                    @endforeach

                    <tr style="background-color: #2a911a; font-weight: bold;">
                        <td></td>
                        <td></td>
                        <td>Voted Voter</td>
                        <td>{{ $totalVoters }}</td>
                        <td>{{ $totalVotedShare }}</td>
                        <td>{{ $votedPercentage }}</td>
                    </tr>
                    <tr style="background-color: #f8d7da; font-weight: bold;">
                        <td></td>
                        <td></td>
                        <td>Not Voted Voter</td>
                        <td>{{ $total_numbers_of_members - $totalVoters }}</td>
                        <td>{{ $total_voting_of_share - $totalVotedShare }}</td>
                        <td>{{ number_format(100 - $votedPercentage, 2) }}</td>
                    </tr>
                    <tr style="background-color: #d4edda; font-weight: bold;">
                        <td></td>
                        <td></td>
                        <td>Total Voter</td>
                        <td>{{ $total_numbers_of_members }}</td>
                        <td>{{ $total_voting_of_share }}</td>
                        <td>100</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="page-break"></div>
        {{-- @endforeach --}}

        <br>
        <div class="table-title">Voting Results</div>
        <table>
            <thead>
                <tr>
                    <th>Voter ID</th>
                    <th>Voter Name</th>
                    <th>Voter Share</th>
                    <th>Item Number</th>
                    <th>Selected Option</th>
                    <th>Date of Voting</th>
                    <th>Modified of Voting Date</th>
                    <th>Status of Vote</th>
                    <th>IP Address</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($resolution->members as $member)
                    @foreach ($resolution->resolution_details as $resolution_detail)
                        @php
                            $votes = $resolution_detail->option_votes()->where('member_id', $member->id)->get();
                        @endphp
                        @foreach ($votes as $vote)
                            <tr>
                                <td>{{ $member->user_name }}</td>
                                <td>{{ $member->name }}</td>
                                <td>{{ $member->share }}</td>
                                <td>{{ $resolution_detail->id }}</td>
                                <td>{{ isset($vote->selected_option) ? $vote->selected_option->label : '' }}</td>
                                <td>{{ isset($vote) ? $vote->created_at : '' }}</td>
                                <td>{{ isset($vote) ? $vote->updated_at : '' }}</td>
                                <td>{{ isset($vote) ? 'VOTED' : 'NOT VOTED' }}</td>
                                <td>{{ isset($vote->ipaddress) ? $vote->ipaddress : '' }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

</body>

</html>
