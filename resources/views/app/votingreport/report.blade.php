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
            <td colspan="19" style="text-align: center">
                <h3>Final Report</h3>
            </td>

        </tr>

        <tr>
            <td>Report Download Date and Time :</td>
            <td>{{ Carbon\Carbon::now()->format('d-M-Y H:i:s') }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            @if ($resolution->user->user_type == 2)
                <td></td>
                <td></td>
                <td></td>
            @endif
        </tr>
        <tr>
            <td>Company Name</td>
            <td>Name of Person</td>
            <td>Voting No.</td>
            <td>Voted Voter</td>
            <td>Total Voter</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            @if ($resolution->user->user_type == 2)
                <td></td>
                <td></td>
                <td></td>
            @endif
        </tr>
        <tr>
            <td>{{ $resolution->company->name }}</td>
            <td>{{ $resolution->user->name }}</td>
            <td>{{ $resolution->id }}</td>
            <td>{{ $resolution->votes->count() / $resolution->resolution_details->count() }}</td>
            <td>{{ $resolution->members->count() }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            @if ($resolution->user->user_type == 2)
                <td></td>
                <td></td>
                <td></td>
            @endif
        </tr>

        <tr>
            <td>Voting Start / End Date and Time:</td>
            <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $resolution->start_date)->format('d-M-Y H:i:s') }}
            </td>

            <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $resolution->end_date)->format('d-M-Y H:i:s') }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            @if ($resolution->user->user_type == 2)
                <td></td>
                <td></td>
                <td></td>
            @endif
        </tr>

        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            @if ($resolution->user->user_type == 2)
                <td></td>
                <td></td>
                <td></td>
            @endif
        </tr>
        <tr>
            <td>ID</td>
            <td>Items</td>
            <td colspan="3">Total Voting Shares</td>

            <td colspan="3">Not Voted (Absent)</td>

            <td colspan="3">Voted Agree (Favour)</td>

            <td colspan="3">Voted Disagree (Against)</td>

            <td colspan="3">Voted Abstain</td>

            {{-- @if ($resolution->user->user_type == 2) --}}
                <td colspan="3">Total Voted</td>
            {{-- @endif --}}
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td>Total Number of Voter</td>
            <td>Voting of Share</td>
            <td>% of Total Share</td>
            <td>No of Not Voted</td>
            <td>Voting of Share</td>
            <td>% of Absent</td>
            <td>No of Voters</td>
            <td>Voting of Share</td>
            <td>% of Agree (Favour)</td>
            <td>No of Voters</td>
            <td>Voting of Share</td>
            <td>% of Disagree (Against)</td>
            <td>No of Voters</td>
            <td>Voting of Share</td>
            <td>% of Abstain</td>
            {{-- @if ($resolution->user->user_type == 2) --}}
                <td>No of Voted</td>
                <td>Voting of Share</td>
                <td>% of Voted</td>
            {{-- @endif --}}
        </tr>
        @foreach ($detailsArr as $resolutionDetail)
            <tr>
                <td>{{ $resolutionDetail['id'] }}</td>
                <td>{{ $resolutionDetail['details'] }}</td>
                <td>{{ $resolutionDetail['total_numbers_of_members'] }}</td>
                <td>{{ $resolutionDetail['total_voting_of_share'] }}</td>
                <td>{{ $resolutionDetail['total_percentage_share'] }}</td>

                <td>{{ $resolutionDetail['absent_no_of_voters'] }}</td>
                <td>{{ $resolutionDetail['absent_voting_of_share'] }}</td>
                <td>{{ $resolutionDetail['absent_percentage'] }}</td>

                <td>{{ $resolutionDetail['assent_no_of_voters'] }}</td>
                <td>{{ $resolutionDetail['assent_voting_of_share'] }}</td>
                <td>{{ $resolutionDetail['assent_percentage'] }}</td>

                <td>{{ $resolutionDetail['dissent_no_of_voters'] }}</td>
                <td>{{ $resolutionDetail['dissent_voting_of_share'] }}</td>
                <td>{{ $resolutionDetail['dissent_percentage'] }}</td>

                <td>{{ $resolutionDetail['abstain_no_of_voters'] }}</td>
                <td>{{ $resolutionDetail['abstain_voting_of_share'] }}</td>
                <td>{{ $resolutionDetail['abstain_percentage'] }}</td>


                {{-- @if ($resolution->user->user_type == 2) --}}
                    <td>{{ $resolutionDetail['total_numbers_of_voters'] }}</td>
                    <td>{{ $resolutionDetail['total_voted_of_share'] }}</td>
                    <td>{{ $resolutionDetail['total_percentage_of_voted'] }}</td>
                {{-- @endif --}}
            </tr>
        @endforeach
    </table>

</body>

</html>
