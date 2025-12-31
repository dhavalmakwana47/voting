<?php

use App\Models\UserLog;
use Illuminate\Support\Facades\Request;

function permissionCheck()
{
    $user = auth()->user();
    if (isset($user) && $user->type == 0) {
        return true;
    }
    return false;
}


function addUserAction($data)
{
    UserLog::create([
        'user_id' => isset($data['user_id']) ? $data['user_id'] : null,
        'member_id' => isset($data['member_id']) ? $data['member_id'] : null,
        'ipaddress' => Request::ip(),
        'resolution_id' => $data['resolution_id'],
        'action' => $data['action']
    ]);
}
