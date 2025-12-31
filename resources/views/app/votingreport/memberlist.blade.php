<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Details Report</title>
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
    <table>
        <tr>
            <th>Voter ID</th>
            <th>Voter Name</th>
            <th>Voter Share</th>
            <th>Item Number</th>
            <th>No of Voter for Agree (Yes)</th>
            <th>No of Votes for Disagree (No)</th>
            <th>No. of Votes for Abstain</th>
            <th>Date of Voting</th>
            <th>Modified of Voting Date</th>
            <th>Status of Vote</th>
            <th>IP Address</th>
        </tr>

        @foreach ($resolution->members as $member)
            @foreach ($resolution->resolution_details as $resolution_detail)
                <tr>
                    <td>{{ $member->user_name }}</td>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->share }}</td>
                    <td>{{ $resolution_detail->id }}</td>
                    <td>{{ isset($member->res_vote->where('resolution_details_id', $resolution_detail->id)->first()->resolution_choice) && $member->res_vote->where('resolution_details_id', $resolution_detail->id)->first()->resolution_choice == 'YES' ? $member->share : 0 }}
                    </td>
                    <td>{{ isset($member->res_vote->where('resolution_details_id', $resolution_detail->id)->first()->resolution_choice) && $member->res_vote->where('resolution_details_id', $resolution_detail->id)->first()->resolution_choice == 'No' ? $member->share : 0 }}
                    </td>
                    <td>{{ isset($member->res_vote->where('resolution_details_id', $resolution_detail->id)->first()->resolution_choice) && $member->res_vote->where('resolution_details_id', $resolution_detail->id)->first()->resolution_choice == 'ABSTAIN' ? $member->share : 0 }}
                    </td>
                    <td>{{ isset($member->vote) ? $member->vote->created_at : '' }}</td>
                    <td>{{ isset($member->vote) ? $member->vote->updated_at : '' }}</td>
                    <td>{{ isset($member->vote) ? 'VOTED' : 'NOT VOTED' }}</td>
                    <td>{{ isset($member->vote->ipaddress) ? $member->vote->ipaddress : '' }}</td>
                </tr>
            @endforeach
        @endforeach
    </table>
</body>

</html>
