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
            <th>Selected Option</th>
            <th>Date of Voting</th>
            <th>Modified of Voting Date</th>
            <th>Status of Vote</th>
            <th>IP Address</th>
        </tr>

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
    </table>
</body>

</html>
