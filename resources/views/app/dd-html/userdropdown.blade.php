<option value="">Select User</option>
@foreach ($users as $user)
    <option value="{{ $user->id }}">{{ $user->name }}</option>
@endforeach