<p> Dear {{ $resolution->user->name }},<br></p>

<p> Please check your member "Delivery Report" using below attachment.</p>

<b>Voting Details:</b>
<ul>
    <li><b>Voting Period:</b>
        [{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $resolution->start_date)->format('d-M-Y h:i A') }}] to
        [{{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $resolution->end_date)->format('d-M-Y h:i A') }}]</li>
    <li><b>Participants:</b> [{{ $resolution->members->count() }}]</li>
</ul>