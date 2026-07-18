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
            /* padding: 20px; */
            background-color: #f4f4f4;
            color: #333;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            border: 1px solid #dddddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        th:hover {
            background-color: #0056b3;
        }

        .center {
            text-align: center;
        }

        h3 {
            margin: 20px 0;
        }

        .bordered-table {
            border: 1px solid #dddddd;
            border-radius: 8px;
            overflow: hidden;
        }

        .table-title {
            font-size: 1.2em;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }

        @media (max-width: 768px) {
            table, th, td {
                font-size: 10px;
                padding: 8px;
            }
        }
    </style>
</head>

<body>
    <div class="bordered-table">
        <table>
            <tr>
                <td colspan="20" class="center">
                    <img src="{{ $logo }}" alt="Admin Logo" style="width: 150px; height: auto; display: block; margin: 0 auto;">
                    <h3>Final Report</h3>
                </td>
            </tr>
            <tr>
                <td>Report Download Date and Time :</td>
                <td colspan="19">{{ Carbon\Carbon::now()->format('d-M-Y H:i:s') }}</td>
            </tr>
            <tr>
                <td>Company Name</td>
                <td>Name of Person</td>
                <td>Voting No.</td>
                <td>Voted Voter</td>
                <td>Total Voter</td>
                <td colspan="17"></td>
            </tr>
            <tr>
                <td>{{ $resolution->company->name }}</td>
                <td>{{ $resolution->user->name }}</td>
                <td>{{ $resolution->id }}</td>
                <td>{{ isset($totalMembersCount) ? (isset($resolution->votes) ? intval($resolution->votes->count() / (isset($totalResolutionDetailsCount) ? $totalResolutionDetailsCount : $resolution->resolution_details->count())) : 0) : ($resolution->votes->count() / $resolution->resolution_details->count()) }}</td>
                <td>{{ isset($totalMembersCount) ? $totalMembersCount : $resolution->members->count() }}</td>
                <td colspan="17"></td>
            </tr>
            <tr>
                <td>Voting Start / End Date and Time:</td>
                <td colspan="2">
                    {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $resolution->start_date)->format('d-M-Y H:i:s') }} -
                    {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $resolution->end_date)->format('d-M-Y H:i:s') }}
                </td>
                <td colspan="20"></td>
            </tr>
            <tr>
                <td></td>
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
                <td>ID</td>
                <td>Resolution Description</td>
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
                <td>{!! nl2br(e($resolutionDetail['details'])) !!}</td>
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
    </div>

    <div class="bordered-table">
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
                <td>{{ isset($member->res_vote->where('resolution_details_id', $resolution_detail->id)->first()->resolution_choice) && $member->res_vote->where('resolution_details_id', $resolution_detail->id)->first()->resolution_choice == 'YES' ? $member->share : 0 }}</td>
                <td>{{ isset($member->res_vote->where('resolution_details_id', $resolution_detail->id)->first()->resolution_choice) && $member->res_vote->where('resolution_details_id', $resolution_detail->id)->first()->resolution_choice == 'No' ? $member->share : 0 }}</td>
                <td>{{ isset($member->res_vote->where('resolution_details_id', $resolution_detail->id)->first()->resolution_choice) && $member->res_vote->where('resolution_details_id', $resolution_detail->id)->first()->resolution_choice == 'ABSTAIN' ? $member->share : 0 }}</td>
                <td>{{ isset($member->vote) ? $member->vote->created_at : '' }}</td>
                <td>{{ isset($member->vote) ? $member->vote->updated_at : '' }}</td>
                <td>{{ isset($member->vote) ? 'VOTED' : 'NOT VOTED' }}</td>
                <td>{{ isset($member->vote->ipaddress) ? $member->vote->ipaddress : '' }}</td>
            </tr>
            @endforeach
            @endforeach
        </table>
    </div>
</body>

</html>
