<option value="">Select Voting</option>
@foreach ($votingrArr as $voting)
    <option value="{{ $voting->id }}">{{ $voting->id . ' (' . $voting->company->name . ')' }}</option>
@endforeach
