<?php

namespace App\Exports;

use App\Models\Resolution;
use App\Models\User;
use App\Models\UserLog;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersLogExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request; // Fix the property assignment
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $user = auth()->user();
        
        // Build the base query for logs
        $logsQuery = UserLog::with(['resolution', 'member', 'user']);

        // Filter logs based on user type
        if ($user->type != 0) {
            $resolutions = Resolution::where('user_id', $user->id)->pluck('id')->toArray();
            $logsQuery->whereIn('resolution_id', $resolutions)->where('user_id',$user->id);
        }

        // Additional filters based on request
        if ($this->request->filled('user_id')) {
            $requestedUser = User::find($this->request->user_id);
            if ($requestedUser && $requestedUser->type != 0) {
                $logsQuery->where('user_id', $this->request->user_id);
            }
        }

        if ($this->request->filled('voting_id')) {
            $logsQuery->where('resolution_id', $this->request->voting_id);
        } else {
            $logsQuery->whereNull('member_id'); // More readable way to check for null
        }

        // Execute the query and map the results
        return $logsQuery->orderBy('id', 'desc')->get()->map(function ($log) {
            return [
                'Date & Time' => Carbon::parse($log->created_at)->format('d-M-Y h:i A'),
                'User Type' => isset($log->member) ? "Voter" : ($log->user->user_type == 1 ? 'AR' : ($log->user->user_type == 2 ? 'Scrutinizer' : 'Admin')),
                'Voting ID' => $log->resolution_id ?? '', // Null coalescing operator
                'Voter ID' => $log->member->user_name ?? '', // Null coalescing operator
                'Member/ User Name' => $log->member->name ?? $log->user->name ?? '', // Combined null checks
                'Action' => $log->action,
                'IP' => $log->ipaddress,
            ];
        });
    }

    // Define the headings
    public function headings(): array
    {
        return [
            'Date & Time',
            'User Type',
            'Voting ID',
            'Voter ID',
            'Member/ User Name',
            'Action',
            'IP',
        ];
    }
}
