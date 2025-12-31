<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voting Receipt</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f2f5fa;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .header {
            background: linear-gradient(135deg, #0061ff, #60efff);
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            letter-spacing: 1px;
        }

        .container {
            max-width: 850px;
            margin: 30px auto;
            padding: 25px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }

        h2 {
            color: #0061ff;
            margin-top: 0;
        }

        p {
            line-height: 1.7;
        }

        .details-list {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }

        .details-list li {
            margin-bottom: 12px;
            padding: 10px 15px;
            background: #f8f9fc;
            border-left: 4px solid #0061ff;
            border-radius: 6px;
        }

        .details-list li b {
            display: inline-block;
            width: 180px;
            color: #444;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f1f5f9;
            color: #333;
        }

        .linkId {
            color: #0061ff;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            margin-top: 8px;
        }

        .linkId:hover {
            text-decoration: underline;
        }

        .footer {
            margin-top: 40px;
            font-size: 14px;
            color: #555;
        }

        .footer p {
            margin: 6px 0;
        }

        .highlight-box {
            background: #e6f3ff;
            padding: 20px;
            border-left: 5px solid #007bff;
            border-radius: 8px;
            margin-bottom: 25px;
        }
    </style>
</head>

<body>



    <div class="container">
        <p><strong>Dear {{ $member->name }},</strong></p>

        <div class="highlight-box">
            We are pleased to inform you that your vote has been successfully recorded through our secure e-voting
            platform. Thank you for actively participating in the voting process.
        </div>

        <h2>Voting Details</h2>
        <ul class="details-list">
            <li><b>Voting Period:</b>
                {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $member->resolution->start_date)->format('d-M-Y h:i A') }}
                to
                {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $member->resolution->end_date)->format('d-M-Y h:i A') }}
            </li>
            <li><b>Company Name:</b> {{ $member->company->name }}</li>
            <li><b>{{ $member->resolution->user->user_type == 1 ? 'AR' : 'Scrutinizer' }} Name:</b>
                {{ $member->resolution->user->name }}</li>
            <li><b>Vote Recorded On:</b>
                {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $votes[0]->updated_at)->format('d-M-Y h:i A') }}</li>
            <li><b>Voting % and Amount:</b> {{ $member->share }}</li>
        </ul>

        <h2>Resolution Summary</h2>
        <table>
            <thead>
                <tr>
                    <th>Item No</th>
                    <th>Voting Description & View</th>
                    <th>Your Choice</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($votes->groupBy('resolution_details_id') as $voteData)
                    @php $vote = $voteData[0]; @endphp
                    <tr>
                        <td>{{ $vote->resolution_details_id }}</td>
                        <td>
                            {!! nl2br(e($vote->resolution_details->description)) !!}<br>
                            <a href="{{ route('memberresolutiondetails.download', Crypt::encrypt($vote->resolution_details_id)) }}" class="linkId">View Information</a>
                        </td>
                        <td>
                            @if ($resolution->evsn_type == '2')
                                @foreach ($voteData as $item)
                                    ({{ $loop->index + 1 }}) {{ $item->selected_option->label }}<br>
                                @endforeach
                            @else
                                @if ($vote->resolution_choice == 'No')
                                    I disagree with the Agenda
                                @elseif($vote->resolution_choice == 'YES')
                                    I agree with the Agenda
                                @else
                                    I abstain from the Agenda
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="footer">
            <p>Need help or have questions?</p>
            <p>Email us at <a href="mailto:info@indiaevoting.com">info@indiaevoting.com</a> or contact Tushar Parikh: <a href="tel:7990822351">7990822351</a></p>
            <p>Thank you once again for participating in E-Voting.</p>
            <p><strong>India E-Voting Services</strong></p>
        </div>
    </div>
</body>

</html>
