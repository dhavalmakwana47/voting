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
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Report Generation Date and Time :</td>
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
            <td></td>
            <td></td>
            <td></td>
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
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Name of Entity</td>
            <td>{{ $resolution->company->name }}</td>
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
            <td></td>
        </tr>
        <tr>
            <td>Name of {{ $resolution->user->user_type != 2 ? 'AR' : 'Scrutinizer'}}</td>
            <td>{{ $resolution->user->name }}</td>
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
            <td></td>
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
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Voting No.</td>
            <td>No. of folios voted</td>
            <td>Total no. of members</td>
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
        </tr>
        <tr>
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
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
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
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Voting Start Date and Time:</td>
            <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $resolution->start_date)->format('d-M-Y H:i:s') }}
            </td>

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
            <td></td>
        </tr>
        <tr>
            <td>Voting End Date and Time:</td>
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
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Voting Finalisation Date and Time:</td>
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
            <td></td>
            <td></td>
            <td></td>
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
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>Resolution</td>
            <td>Voted Assent</td>
            <td></td>
            <td></td>
            <td>Voted Dissent</td>
            <td></td>
            <td></td>
            <td>Voted Abstain</td>
            <td></td>
            <td></td>
            <td>Not Voted (Absent)</td>
            <td></td>
            <td></td>
            <td>Total Voting Shares</td>
            <td></td>
            <td></td>
            <td>Total Voted</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>No of Voters</td>
            <td>Voting of Share</td>
            <td>% of Assent</td>
            <td>No of Voters</td>
            <td>Voting of Share</td>
            <td>% of Dissent</td>
            <td>No of Voters</td>
            <td>Voting of Share</td>
            <td> % of Abstain</td>
            <td>No of Not Voted</td>
            <td>Voting of Share</td>
            <td>% of Absent</td>
            <td>No of Members</td>
            <td>Voting of Share</td>
            <td>% of Total Share</td>
            @if ($resolution->user->user_type == 2)
                <td>No of Voted</td>
                <td>Voting of Share</td>
                <td>% Voted</td>
            @endif
        </tr>
        @foreach ($detailsArr as $resolutionDetail)
            <tr>
                <td>{{ $resolutionDetail['details'] }}</td>
                <td>{{ $resolutionDetail['assent_no_of_voters'] }}</td>
                <td>{{ $resolutionDetail['assent_voting_of_share'] }}</td>
                <td>{{ $resolutionDetail['assent_percentage'] }}</td>
                <td>{{ $resolutionDetail['dissent_no_of_voters'] }}</td>
                <td>{{ $resolutionDetail['dissent_voting_of_share'] }}</td>
                <td>{{ $resolutionDetail['dissent_percentage'] }}</td>
                <td>{{ $resolutionDetail['abstain_no_of_voters'] }}</td>
                <td>{{ $resolutionDetail['abstain_voting_of_share'] }}</td>
                <td>{{ $resolutionDetail['abstain_percentage'] }}</td>
                <td>{{ $resolutionDetail['absent_no_of_voters'] }}</td>
                <td>{{ $resolutionDetail['absent_voting_of_share'] }}</td>
                <td>{{ $resolutionDetail['absent_percentage'] }}</td>
                <td>{{ $resolutionDetail['total_numbers_of_members'] }}</td>
                <td>{{ $resolutionDetail['total_voting_of_share'] }}</td>
                <td>{{ $resolutionDetail['total_percentage_share'] }}</td>
                @if ($resolution->user->user_type == 2)
                    <td>{{ $resolutionDetail['total_numbers_of_voters'] }}</td>
                    <td>{{ $resolutionDetail['total_voted_of_share'] }}</td>
                    <td>{{ $resolutionDetail['total_percentage_of_voted'] }}</td>
                @endif
            </tr>
        @endforeach
    </table>

</body>

</html>
