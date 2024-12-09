<?php

use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\OSSPIDLoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Modules\REUSELicenseIssue\Http\Controllers\AjaxRequestController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('logs', [\Rap2hpoutre\LaravelLogViewer\LogViewerController::class, 'index']);

Route::post('login/load-login-otp-form', [LoginController::class, 'loadLoginOtpForm']);
Route::post('login/otp-login-validation-with-token-provide', [LoginController::class, 'otpLoginEmailValidationWithTokenProvide']);
Route::post('login/otp-login-check', [LoginController::class, 'checkOtpLogin']);
Route::post('login/otp-resent', [LoginController::class, 'OtpResent']);
Route::post('login/check-sms-send-status', [LoginController::class, 'checkSMSstatus']);

Route::post('search-pilgrim-by-tracking-no', [LoginController::class, 'searchPilgrimByTrackingNo']);
Route::post('search-pilgrim-by-access-token', [LoginController::class, 'searchPilgrimByAccessToken']);
Route::post('save-and-search-pilgrim', [LoginController::class, 'saveNsearchPilgrim']);
Route::post('prp-login', [LoginController::class, 'prpLogin']);
Route::post('resend-otp-to-user', [LoginController::class, 'resendOtpToUser']);
Route::get('contact', [LoginController::class, 'contact']);
Route::get('privacy-policy', [LoginController::class, 'privacyPolicy']);
Route::get('ehaj-apps', [LoginController::class, 'ehajApps']);

Route::group(array('middleware' => ['auth']), function () {
    Route::get('logout', [LoginController::class, 'logout'])->name('logout');
});

Route::post('/getDropDownData', [AjaxRequestController::class, 'dropDownList']);
Route::post('/getSubTeamData', [AjaxRequestController::class, 'getSubTeamData']);



/*
 * Google Login routes
 */
Route::get('auth/google', [GoogleLoginController::class, 'redirectToProvider']);
Route::get('auth/google/callback', [GoogleLoginController::class, 'handleProviderCallback']);
Route::get('oauth/google/callback', [GoogleLoginController::class, 'handleProviderCallback']);


//OSSPID LOGIN and signup
Route::get('osspid-callback', [OSSPIDLoginController::class, 'osspidCallback']);
Route::get('osspid_signUp', [OSSPIDLoginController::class, 'osspid_signUp']);
Route::patch('osspid/store', [OSSPIDLoginController::class, 'OsspidStore']);
Route::get('osspid/logout', [OSSPIDLoginController::class, 'osspidLogout']);

//General LOGIN and signup
Route::post('login/check', [LoginController::class, 'check']);

/*bscic attachment*/

Route::get('bscic-attachment/{fileurl}', [CommonController::class, 'getAttachment']);
Route::post('getCountData', [CommonController::class, 'getDynamicCountData']);
Route::get('get-notice-list', [CommonController::class, 'getNoticeList']);
Route::get('get-schedule-flight', [CommonController::class, 'getFlightList']);

Route::get('/get-public-page-api', [CommonController::class, 'getPublicPageApi']);
Route::get('/get-selected-news/{year}', [CommonController::class, 'getSelectedNews']);
Route::get('/all-news',[CommonController::class, 'allNews']);
// Footer Important Link
Route::get('/get-impotant-link', [CommonController::class, 'getImpotantLink']);
// Forget password routes
Route::get('re-captcha', [LoginController::class, 'reCaptcha']);
Route::get('forget-password', [LoginController::class, 'forgetPassword']);
Route::post('reset-forgotten-password', [LoginController::class, 'resetForgottenPass']);
Route::get('verify-forgotten-pass/{token_no}', [LoginController::class, 'verifyForgottenPass']);
Route::post('store-forgotten-password', [LoginController::class, 'StoreForgottenPass']);
Route::get('get-pilgrimdb-path', [LoginController::class, 'getPilgrimdbPath']);

#Route::get('articles/support', [ArticlesController::class, 'aboutQuickServicePortal']);

//////Route::get('searchPilgrimByTrackingNo/{token_no}', [LoginController::class, 'searchPilgrimByTrackingNo']);



Route::group(array('middleware' => ['auth']), function () {
    Route::get('common/activities/activities-summary', [CommonController::class, 'activitiesSummary']);
});
Route::get('/keycloak/callback', [LoginController::class, 'kecloakCallback']);
