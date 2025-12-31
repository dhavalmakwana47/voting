<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <p> Dear {{ $resolution->user->name }},<br></p>

    <p> I am pleased to inform you that the electronic voting (e-voting) process for the upcoming
        "{{ $resolution->company->name }}” has been
        successfully completed. Your careful consideration and approval of this crucial step are greatly
        appreciated.</p>

    <b>Voting Details:</b>
    <ul>
        <li><b>Voting Period:</b>
            [{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $resolution->start_date)->format('d-M-Y h:i A') }}] to
            [{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $resolution->end_date)->format('d-M-Y h:i A') }}]</li>
        <li><b>Participants:</b> [{{ $resolution->members->count() }}]</li>
    </ul>
</body>
</html>