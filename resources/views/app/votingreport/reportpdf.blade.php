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
            line-height: 1.6;
            margin: 20px;
            background-color: #f9f9f9;
        }

        h3 {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin: 20px 0;
            font-size: 16px;
            text-align: left;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #4CAF50;
            color: white;
            position: sticky;
            top: 0;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #e1f5fe;
        }

        .header-container {
            text-align: center;
            padding: 20px;
            margin-bottom: 20px;
        }

        .logo {
            width: 150px;
            height: auto;
        }

        .summary-info {
            margin-bottom: 20px;
        }

        .summary-info td {
            padding: 10px;
        }
    </style>
</head>

<body>
    <div class="header-container">
        <img src="{{ $logo }}" alt="AdminLTE Logo" class="logo">
        <h3>Final Report</h3>
    </div>

    <table class="summary-info">
        <tr>
            <td>Report Download Date and Time:</td>
            <td>{{ Carbon\Carbon::now()->format('d-M-Y H:i:s') }}</td>
        </tr>
        <tr>
            <td>Company Name:</td>
            <td>{{ $resolution->company->name }}</td>
        </tr>
        <tr>
            <td>Name of Person:</td>
            <td>{{ $resolution->user->name }}</td>
        </tr>
        <tr>
            <td>Voting No.:</td>
            <td>{{ $resolution->id }}</td>
        </tr>
        <tr>
            <td>Voted Voter:</td>
            <td>{{ isset($totalMembersCount) ? (isset($resolution->votes) ? intval($resolution->votes->count() / (isset($totalResolutionDetailsCount) ? $totalResolutionDetailsCount : $resolution->resolution_details->count())) : 0) : ($resolution->votes->count() / $resolution->resolution_details->count()) }}</td>
        </tr>
        <tr>
            <td>Total Voter:</td>
            <td>{{ isset($totalMembersCount) ? $totalMembersCount : $resolution->members->count() }}</td>
        </tr>
        <tr>
            <td>Voting Start Date and Time:</td>
            <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $resolution->start_date)->format('d-M-Y H:i:s') }}
            </td>
        </tr>
        <tr>
            <td>Voting End Date and Time:</td>
            <td>{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $resolution->end_date)->format('d-M-Y H:i:s') }}</td>
        </tr>
    </table>

    <h3>Resolution Details</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Description</th>
            <th>% of Total Share</th>
            <th>% of Absent</th>
            <th>% of Agree (Favour)</th>
            <th>% of Disagree (Against)</th>
            <th>% of Abstain</th>
            <th>% of Voted</th>
        </tr>
        @foreach ($detailsArr as $resolutionDetail)
            <tr>
                <td>{{ $resolutionDetail['id'] }}</td>
                <td>{!! nl2br(e( $resolutionDetail['details'])) !!}</td>
                <td>{{ $resolutionDetail['total_percentage_share'] }}</td>
                <td>{{ $resolutionDetail['absent_percentage'] }}</td>
                <td>{{ $resolutionDetail['assent_percentage'] }}</td>
                <td>{{ $resolutionDetail['dissent_percentage'] }}</td>
                <td>{{ $resolutionDetail['abstain_percentage'] }}</td>
                <td>{{ $resolutionDetail['total_percentage_of_voted'] }}</td>
            </tr>
        @endforeach
    </table>

    <h3>Voting Details</h3>
    <table>
        <tr>
            <th>Voter ID</th>
            <th>Voter Name</th>
            <th>Voter Share</th>
            <th>ID</th>
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
