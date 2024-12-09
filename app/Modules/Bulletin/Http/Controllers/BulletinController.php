<?php

namespace App\Modules\Bulletin\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\Encryption;
use Yajra\DataTables\DataTables;
use App\Libraries\CommonFunction;
use App\Modules\News\Models\News;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Expr\Cast\Object_;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\Modules\Settings\Models\Configuration;
use App\Modules\Bulletin\Models\BulletinMaster;
use App\Modules\Bulletin\Services\BulletinService;
use App\Modules\Bulletin\Exceptions\BulletinException;
use App\Modules\Bulletin\Http\Requests\BulletinRequest;

class BulletinController extends Controller
{
    private function getTokenData(){
        $tokenUrl =  env('API_URL')."/api/getToken";
        $credential = [
            'clientid' => env('CLIENT_ID'),
            'username' => env('CLIENT_USER_NAME'),
            'password' => env('CLIENT_PASSWORD')
        ];

        return CommonFunction::getApiToken($tokenUrl, $credential);
    }
    public function create()
    {
        $lastBulletinData = BulletinMaster::latest()->first();
        $fixedText = '';
        $lastBulletinHajType = '';
        if(!empty($lastBulletinData)){
            $fixedText = $lastBulletinData->fixed_text;
            $lastBulletinHajType = $lastBulletinData->haj_type;
        }
        return view('Bulletin::create', compact('fixedText', 'lastBulletinHajType'));
    }
    public function preview(BulletinRequest $request, BulletinService $bulletinService)
    {
        try {
            $bulletinReqData = (object) $request->validated();
            # Determine if edit mode is needed
            $editMode = $this->isInEditMode($bulletinReqData);
            $bulletinData = $bulletinService->preview($bulletinReqData, $editMode);
            return view('Bulletin::preview', $bulletinData);
        } catch (BulletinException|\Exception $exception) {
            Session::flash('error', $exception->getMessage());
            return redirect()->back();
        }

    }
    public function edit(Request $request)
    {
        try {
            $encryptId = $request->segment(3);
            $bulletinId = Encryption::decodeId($encryptId);
            $singleBulletinInfo = BulletinMaster::where('id', $bulletinId)->first();
            $bulletinNewsData = News::where('bulletin_master_id',$bulletinId)->select('id')->first();

            return view('Bulletin::edit', compact('singleBulletinInfo','bulletinNewsData'));

        } catch (BulletinException|\Exception $exception) {
            DB::rollBack();
            Session::flash('error', $exception->getMessage());
            return redirect()->back();
        }

    }
    public function store(Request $request, BulletinService $bulletinService)
    {
        try {
            $encryptedBulletinData = $request->get('serialized_bulletin_info');
            $bulletinData = (object) unserialize(Encryption::decode($encryptedBulletinData));
            $encryptedBulletinTemplate = $request->get('serialized_bulletin_template');
            $bulletinTemplate = unserialize(Encryption::decode($encryptedBulletinTemplate));
            $flag = $request->get('flag');
            DB::beginTransaction();
            # Determine if edit mode is needed
            $editMode = $this->isInEditMode($bulletinData);
            $bulletinService->publish($bulletinData, $bulletinTemplate, $editMode, $flag);
            DB::commit();
            Session::flash('success', "বুলেটিন সফলভাবে ".($editMode ? 'আপডেট ' : 'সংরক্ষণ')." করা হয়েছে৷");
            return redirect()->route('bulletin_list');

        } catch (BulletinException|\Exception $exception) {
            DB::rollBack();
            Session::flash('error', $exception->getMessage());
            return redirect()->back();
        }
    }

    public function list()
    {
        $access_users = Configuration::where('caption','bulletin_permission')->first();
        if(empty($access_users)){
            Session::flash('error', 'Invalid URL ! This incident will be reported.');
            return redirect('dashboard');
        }
        $access_users_array = json_decode($access_users->value2);
        if(!in_array(Auth::user()->user_email,$access_users_array)){
            Session::flash('error', 'Invalid URL ! This incident will be reported.');
            return redirect('dashboard');
        }
        return view('Bulletin::list');
    }

    public function getBulletinList(){
        $user_type = Auth::user()->user_type;
        $token = $this->getTokenData();
        $headers = array(
            'APIAuthorization: bearer ' . $token,
            'Content-Type: application/json',
        );
        $base_url = env('API_URL');
        $apiUrl = "$base_url/api/get-bulletin-list";
        $apiResponse = CommonFunction::curlPostRequest($apiUrl,[],$headers);
        $apiResponseDataArr = json_decode($apiResponse['data']);
        $data = !empty($apiResponseDataArr->data) ? $apiResponseDataArr->data : [];
        return Datatables::of($data)
        ->addColumn('open', function ($data) {
            return '<div class="btn-flex-center">
                        <a class="btn btn-primary btn-sm" href="' . url('/bulletin/edit/' . Encryption::encodeId($data->id)) .'">
                            <span>Open</span>
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="5" height="9" viewBox="0 0 5 9" fill="none">
                                    <path d="M1.29492 1.29165L4.50326 4.49998L1.29492 7.70831" stroke="white" stroke-width="0.6875" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </span>
                        </a>
                    </div>';
        })
        ->rawColumns(['open'])
        ->make(true);
    }

    function isInEditMode($bulletinData): bool
    {
        // return !empty($bulletinData) && (!empty($bulletinData->bulletinId) && !empty($bulletinData->bulletinNewsId));
        return !empty($bulletinData) && (!empty($bulletinData->bulletinId) && (!empty($bulletinData->bulletinNewsId) || $bulletinData->bulletinNewsId === null));
    }


}
