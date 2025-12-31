<!-- resources/views/app/votingreport/header.blade.php -->
<div style="width: 100%; text-align: center; border-bottom: 1px solid #000; margin-bottom: 20px;">
    <img src="{{ $logo }}" alt="Company Logo" style="height: 50px;"> <!-- Display logo -->
    <h2>{{ isset($resolution->company) ? $resolution->company->name : 'N/A' }}</h2>
    <p>Name of Person: {{ isset($resolution->user) ? $resolution->user->name : 'N/A' }}</p>
    <p>Voting No.: {{ $resolution->id }}</p>
    <p style="font-size: 12px;">Page {PAGE_NUM} of {PAGE_COUNT}</p>
</div>
