<?php

namespace App\Http\Middleware;

use App\Models\Member;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MemberLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if ($request->session()->has('member_login')) {
            $now = Carbon::now();
            $loginDate = session('login_date');

            if (!$now->isSameDay($loginDate)) {
                $request->session()->flush();
            } else {
                $members = Member::where([[session('login_type'),  session('login_by')], ['is_active', 1]])->get();
                if (count($members) > 0) {
                    return $next($request);
                } else {
                    $request->session()->flush();
                }
            }
        }

        return redirect()->route('member.login');
    }
}
