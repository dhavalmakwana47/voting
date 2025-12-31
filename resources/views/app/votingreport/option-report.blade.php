<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Summary Report</title>
    <style>
        table {
            border-collapse: collapse;
            border: 1px solid black;
        }

        th,
        td {
            border: 1px solid black;
            padding: 8px;
        }
    </style>
</head>

<body>
    <table class="bordered-table">
        <tr>
            <td colspan="19" style="text-align: center; padding: 20px;">
                <h3 style="margin-top: 20px;">Final Report</h3>
            </td>

        </tr>

        <tr>
            <td>Report Download Date and Time :</td>
            <td>{{ Carbon\Carbon::now()->format('d-M-Y H:i:s') }}</td>

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
            <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $resolution->start_date)->format('d-M-Y H:i:s') }}
            </td>

            <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $resolution->end_date)->format('d-M-Y H:i:s') }}</td>
            <td></td>
            <td></td>


        </tr>
    </table>
    <br>
    @php
        $totalMembers = $resolution->members;
        $total_voting_of_share = $totalMembers->sum('share');
        $total_numbers_of_members = $totalMembers->count();
    @endphp
    <table border="1" cellpadding="6" cellspacing="0"
        style="width: 100%; border-collapse: collapse; font-family: sans-serif;">
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

                <tr>
                    <td rowspan="{{ count($labels) + 4 }}" style="vertical-align: top; padding-top: 8px;">
                        {!! nl2br(e($resolutionDetail->id)) !!}
                    </td>
                    <td rowspan="{{ count($labels) + 4 }}" style="vertical-align: top; padding-top: 8px;">
                        {!! nl2br(e($resolutionDetail->description)) !!}
                    </td>

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
                    <td style="{{ $highlightStyle }}">{{ $label->label }}</td>
                    <td style="{{ $highlightStyle }}">{{ $label->option_votes->count() }}</td>
                    <td style="{{ $highlightStyle }}">{{ $currentVotedShare }}</td>
                    <td style="{{ $highlightStyle }}">{{ $currentPercentage }}</td>
                </tr>
            @endforeach

            <tr style="background-color: #e2e3e5; font-weight: bold;">
                <td>Voted Voter</td>
                <td>{{ $totalVoters }}</td>
                <td>{{ $totalVotedShare }}</td>
                <td>{{ $votedPercentage }}</td>
            </tr>
            <tr style="background-color: #f8d7da; font-weight: bold;">
                <td>Not Voted Voter</td>
                <td>{{ $total_numbers_of_members - $totalVoters }}</td>
                <td>{{ $total_voting_of_share - $totalVotedShare }}</td>
                <td>{{ number_format(100 - $votedPercentage, 2) }}</td>
            </tr>
            <tr style="background-color: #d4edda; font-weight: bold;">
                <td>Total Voter</td>
                <td>{{ $total_numbers_of_members }}</td>
                <td>{{ $total_voting_of_share }}</td>
                <td>100</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
