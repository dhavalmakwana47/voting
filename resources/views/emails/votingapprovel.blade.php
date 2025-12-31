<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
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
   <p> Dear {{ $resolution->user->name }},<br></p>

    <p> I am pleased to inform you that the electronic voting (e-voting) process for the upcoming
        "{{ $resolution->company->name }}” has been
        officially approved and activated. Your careful consideration and approval of this crucial step are greatly
        appreciated.</p>

    <b>Voting Details:</b>
    <ul>
        <li><b>Voting Period:</b>
            [{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $resolution->start_date)->format('d-M-Y h:i A') }}] to
            [{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $resolution->end_date)->format('d-M-Y h:i A') }}]</li>
        <li><b>Participants:</b> [{{ $resolution->members->count() }}]</li>
    </ul>


    <h2>E-Voting Description Details</h2>
    <table>
        <tr>
            <th>Item No</th>
            <th>E-Voting Description</th>
        </tr>

        @foreach ($resolution->resolution_details()->orderBy('index')->get() as $resolution_detail)
            <tr>
                <td>{{ $resolution_detail->id }}</td>
                <td>{!! nl2br(e($resolution_detail->description)) !!}<br>
                    <a href="{{ route('memberresolutiondetails.download', Crypt::encrypt($resolution_detail->id)) }}" class="linkId">View
                        Information</a>
                </td>
            </tr>
        @endforeach

    </table>



    <p>Should you have any questions or require additional assistance, please do not hesitate to contact me at Tushar
        Parikh (7990822351)</p>
    <p>Thank you for your commitment to upholding the democratic values of our organization.</p>
    <p>Best regards,</p>
    <p>For India E-Voting Services</p>

</body>

</html>
