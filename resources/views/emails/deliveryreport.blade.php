<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Delivery Report</title>
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
        <thead>
            <tr>
                <th>Name</th>
                <th>Share</th>
                <th>Email</th>
                <th>Contact No</th>
                <th>Email Status</th>
                <th>Reason</th>
                <th>Send Date Time</th>
                <th>Delivery Date Time</th>

            </tr>
        </thead>
        <tbody>
            @foreach ($members as $member)
                <tr>
                    <td>{{ $member->name }}</td>
                    <td>{{ $member->share }}</td>
                    <td>{{ $member->email }}</td>
                    <td>{{ $member->phone }}</td>
                    <td>{{ $member->email_sent == 'N' ? 'NOT SENT' : 'SENT' }}</td>
                    <td>{{ $member->reason }}</td>
                    <td>{{ (new DateTime($member->sent_date))->format('d-M-Y h:i A') }}</td>
                    <td>{{ (new DateTime($member->delivery_date))->format('d-M-Y h:i A') }}</td>
                </tr>
            @endforeach
        </tbody>

    </table>
</body>

</html>
