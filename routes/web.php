<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OptionVoteController;
use App\Http\Controllers\OptionVotingController;
use App\Http\Controllers\ResolutionController;
use App\Http\Controllers\UserCompanyMapController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserLogController;
use App\Http\Controllers\VoteController;
use App\Http\Controllers\VotingReportController;
use App\Models\UserLog;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


/* <============================  CompanyController  ============================> */

Route::get('companyadd', [CompanyController::class, "addcompany"])->name('company.add');
Route::post('companyadd', [CompanyController::class, "storecompany"])->name('company.storecompany');
Route::get('/policy', function () {
    return view('policy');
})->name('policy');
Route::get('/sentmailapproval', [MailController::class,'sendMailApprovedCron']);

Route::get('/sentfinalreport', [MailController::class,'sendFinalReportCron']);

/* <============================  CompanyController  ============================> */
Route::get('/', function () {
    return view('welcome');
})->name('index');

Route::group(['middleware' => ['auth', 'userstatus']], function () {
    /* <============================  The Start  ============================> */

    Route::get('/dashboard',[DashboardController::class,'index'])->name('home');


   

    /* <============================  UserLogController  ============================> */
    Route::get('userlog/', [UserLogController::class, "index"])->name('userlog.index');
    Route::post('userlog/download', [UserLogController::class, "userlog_downlaod"])->name('userlog.download');
    Route::post('userlog/getusers', [UserLogController::class, "getusers"])->name('userlog.users');
    Route::post('userlog/get_votings', [UserLogController::class, "get_votings"])->name('userlog.get_votings');

    /* <============================  UserController  ============================> */

    Route::resource('users', UserController::class)->middleware(['permissioncheck']);
    Route::post('users/changestatus', [UserController::class, "changeStatus"])->name('users.changestatus');
    Route::get('user/change-password', [UserController::class, 'showChangePasswordForm'])->name('userpassword.change');
    Route::post('user/change-password', [UserController::class, 'updatePassword'])->name('userpassword.update');

    /* <============================  CompanyController  ============================> */

    Route::resource('company', CompanyController::class)->middleware(['permissioncheck']);
    Route::post('company/changestatus', [CompanyController::class, "changeStatus"])->name('company.changestatus');

    /* <============================  UserController  ============================> */
    Route::resource('usercompanymap', UserCompanyMapController::class)->middleware(['permissioncheck']);
    Route::post('assign_usrs', [UserCompanyMapController::class, 'assign_users'])->name('usercompanymap.assign_users');

    /* <============================  ResolutionController  ============================> */

    Route::resource('option-voting', OptionVotingController::class);
    Route::post('add-section', [OptionVotingController::class, 'add_section'])->name('voting.add_section');

    Route::resource('voting', ResolutionController::class);
    Route::post('voting-option', [ResolutionController::class, 'upload'])->name('voting.upload');
    Route::get('voting-upload', [ResolutionController::class, 'create_option_voting'])->name('voting.create_option_voting');
    Route::get('voting-download', [ResolutionController::class, 'downloadFile'])->name('voting.sample-download');
    Route::post('voting-changestatus', [ResolutionController::class, "changeStatus"])->name('voting.changestatus')->middleware(['permissioncheck']);
    Route::get('votingdetails/download/{id}', [ResolutionController::class, 'resolutionDetailsFile'])->name('votingdetails.download');

    /* <============================  MemberController  ============================> */

    Route::get('voter/voter-detail/{resolution_id}', [MemberController::class, 'index'])->name('member.index');
    Route::post('voter/voter-detail-export/{resolution_id}', [MemberController::class, 'exportData'])->name('member.list-export');

    Route::get('voter/voter-report/{resolution_id}', [MemberController::class, 'member_report'])->name('member.member_report');
    Route::post('voter/delete', [MemberController::class, 'destroy'])->name('member.delete');
    Route::post('voter/store', [MemberController::class, 'store'])->name('member.store');
    Route::get('voter/edit/{id}', [MemberController::class, 'edit'])->name('member.edit');
    Route::post('voter/update', [MemberController::class, 'update'])->name('member.update');
    Route::get('voter/list/{resolution_id}', [MemberController::class, 'member_list'])->name('member.list');
    Route::get('voter/sharecounnt/{resolution_id}', [MemberController::class, 'share_count'])->name('member.share_count');
    Route::get('voter/mail/{id}', [MemberController::class, 'resend_mail'])->name('member.resend_mail');
    Route::get('voter/sms/{id}', [MemberController::class, 'resend_sms'])->name('member.sms');

    /* <============================  VoteController  ============================> */

    Route::post('vote/list', [VoteController::class, 'list'])->name('vote.list');
    Route::get('vote/status', [VoteController::class, 'index'])->name('vote.index');
    Route::get('vote/sharecounnt', [VoteController::class, 'share_count'])->name('vote.share_count');

    /* <============================  Voteing Report Controller  ============================> */
    Route::any('votingreport', [VotingReportController::class, 'index'])->name('votingreport.index');
    Route::get('votingreport/report/{type}/{id}', [VotingReportController::class, 'get_report'])->name('votingreport.get_report');
    Route::get('votingreport-option/report/{type}/{id}', [VotingReportController::class, 'option_report'])->name('option_report.get_report');
    Route::get('votingreport/new-report/{id}', [VotingReportController::class, 'new_report'])->name('votingreport.new_report');
    Route::get('votingreport/new-report-view/{id}', [VotingReportController::class, 'new_report_view'])->name('votingreport.new_report_view');

    /* <============================  The End  ============================> */
});


/**
 * ============================>============================>==============>
 *                              Member: Login Routes
 * ============================>============================>==============>
 */
Route::get('voter/login', [MemberController::class, 'member_login'])->name('member.login');
Route::post('voter/login', [MemberController::class, 'login'])->name('member.loginvalidate');
Route::post('voter/voterexist', [MemberController::class, 'memberexist'])->name('member.memberexist');
Route::post('voter/resendotp', [MemberController::class, 'resendOTP'])->name('member.resendotp');


/**
 * ============================>============================>==============>
 *                              Member: After Login Routes
 * 
 * ============================>============================>==============>
 */
Route::group(['middleware' => 'memberlogin'], function () {
    Route::get('voter/logout', [MemberController::class, 'logout'])->name('member.logout')->middleware(['memberlogin']);
    Route::get('voter/voting-list', [MemberController::class, 'voting_list'])->name('member.voting_list')->middleware(['memberlogin']);
    Route::get('voter/add-voting/{resolution_id}', [MemberController::class, 'add_voting'])->name('member.add_voting')->middleware(['memberlogin']);
    Route::post('vote/store', [VoteController::class, 'store'])->name('vote.store')->middleware(['memberlogin']);
    Route::get('vote/recipt/{member_id}', [VoteController::class, 'voting_recipt'])->name('vote.voting_recipt');

    Route::get('voter/change-password', [MemberController::class, 'change_password'])->name('member.change_password')->middleware(['memberlogin']);
    Route::post('voter/change-password', [MemberController::class, 'update_password'])->name('member.update_password')->middleware(['memberlogin']);

    Route::post('option-vote/store', [OptionVoteController::class, 'store'])->name('option_vote.store')->middleware(['memberlogin']);
    Route::post('member/send-voting-otp', [MemberController::class, 'sendVotingOtp'])->name('member.send_voting_otp')->middleware(['memberlogin']);
    Route::post('member/verify-voting-otp', [MemberController::class, 'verifyVotingOtp'])->name('member.verify_voting_otp')->middleware(['memberlogin']);

});
Route::get('voterresolutionsdetails/download/{id}', [ResolutionController::class, 'resolutionDetailsFile'])->name('memberresolutiondetails.download');
Auth::routes();

/* <============================  Routes End  ============================> */