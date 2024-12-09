<?php

namespace App\Http\Middleware;

use App\Http\Controllers\LoginController;
use App\Libraries\CommonFunction;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class checkAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        /*
         * Redirect URL must should be call outside of checkAdmin middleware
         */
        $user_type = Auth::user()->user_type;
        $user = explode("x", $user_type); // $user[0] array index stored the users level id

        $security_check_time = Session::get('security_check_time');
        $current_time = Carbon::now();
        $difference_in_minute = $current_time->diffInMinutes($security_check_time);

        /*
         * Some common conditions will be checked periodically. (Ex: after every 3 minutes and after login)
         * If there is a condition that needs to be checked for each URL,
         * then it has to be given above this condition.
         */
        if ($difference_in_minute >= 3 or (Session::get('is_first_security_check') == 0)) {

            Session::put('is_first_security_check', 1);
            $security_check_time = Carbon::now();
            Session::put('security_check_time', $security_check_time);

            // check the user is approved
            if (Auth::user()->is_approved == 0) {
                return redirect()
                    ->intended('/dashboard')
                    ->with('error', 'You are not approved user ! Please contact with system admin');
            }

            // while user try to login
            $LgController = new LoginController;
            if (!$LgController->_checkSecurityProfile($request)) {
                Auth::logout();
                return redirect('/login')
                    ->with('error', 'Security profile does not support in this time for operation.');
            }

        }

        // But, for others module/application it is mandatory
        if (CommonFunction::checkEligibility() != 1 and (in_array($user_type, ['5x505']))) {
            Session::flash('error', 'You are not eligible for apply ! [CAM1020]');
            return redirect('dashboard');
        }

        $uri = $request->segment(1);
        if($uri == 'client' || $uri == 'vue'){
            $uri = $request->segment(2);
        }

        switch (true) {
            case ($uri == 'dashboard' and (in_array($user[0], [1, 2, 3, 4, 5, 6]))):
            case ($uri == 'medicine-store' and (in_array($user[0], [17]))):
            case ($uri == 'users' and (in_array($user[0], [1, 2, 4, 5, 6, 8]))):
            case ($uri == 'guide-users' and (in_array($user[0], [1, 2, 4, 5, 6, 8]))):
            case ($uri == 'company-association' and (in_array($user[0], [1, 2, 4, 5, 6, 7, 8]))):
            case ($uri == 'documents' and (in_array($user[0], [1, 2, 4, 5, 6]))):
            case ($uri == 'process-path' and (in_array($user[0], [1]))):
            case ($uri == 'industry-new' and (in_array($user[0], [1, 4, 5, 6]))):
            case ($uri == 'company-profile' and (in_array($user[0], [5]))):
            case ($uri == 'industry-re-registration' and (in_array($user[0], [1, 4, 5, 6]))):
            case ($uri == 'spg' and (in_array($user[0], [1, 4,5, 6]))):
            case ($uri == 'ipn' and (in_array($user[0], [1,2]))):
            case ($uri == 'settings' and (in_array($user[0], [1,2]))):
            case ($uri == 'room-allocation'):
                return $next($request);
            case ($uri == 'pay-order-received'):
                return $next($request);
            case ($uri == 'flight'):
                return $next($request);
            case ($uri == 'bulletin'):
                return $next($request);
            case ($uri == 'pilgrim'):
                return $next($request);
            case ($uri == 'registration'):
                return $next($request);
            case ($uri == 'guides'):
                return $next($request);
            default:
                Session::flash('error', 'Invalid URL ! error code(' . $uri . '-' . $user[0] . ')');
                return redirect('dashboard');
        }
    }
}
