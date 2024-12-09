<?php

namespace App\Modules\ProfileInfoPdf\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Libraries\PostApiData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mpdf\Mpdf;
use PHPUnit\Exception;


class ProfileInfoPdfController extends Controller
{

    /**
     * Display the module welcome screen
     *
     * @return \Illuminate\Http\Response
     */
    public function welcome()
    {
        return view("Listing::welcome");
    }

    public function PilgrimListing(Request $request){
        $data = array();
        //$public_html = strval(view("Listing::application-form", $data));
        //return response()->json(['responseCode' => 1, 'html' => $public_html]);

        return view("Listing::application-form", $data);
    }
    public function profileInfo($tracking_no){
        $tracking_no = Encryption::decodeId($tracking_no);
        $is_pilgrim_profile = (Auth::user()->user_type == '21x101');
        if($is_pilgrim_profile){

                $postData = [
                    'tracking_no' => $tracking_no,
                    'is_child_list' => 1
                ];
                $postdata = http_build_query($postData);
                $base_url = env('API_URL');
                $url = "$base_url/api/get-pilgrim-profile-information";
                $response = PostApiData::getData($url,$postdata);
                $response_data = json_decode($response,true);
                if ($response_data['status'] == 200) {
                    $profileInfo = $response_data['data'];
                    if(isset($profileInfo['basic_info']['pdf_flag'])) {
                        $check_pdf_flag = $profileInfo['basic_info']['pdf_flag'];
                        if ($check_pdf_flag) {
                            $archived_pilgrim = $check_pdf_flag;
                        }
                    }else{
                        $archived_pilgrim = 'true';
                    }
                }

        }else{
            return redirect()->back()->with('error',"Pdf profile not found");
        }
        if(isset($profileInfo) && $profileInfo!=null){
            $html = strval( view("ProfileInfoPdf::profile_info",compact('profileInfo','archived_pilgrim')));
            $title = 'Profile Info PDF';
            $subject = '';
            $stylesheet= file_get_contents(public_path("assets/stylesheets/profile_info_pdf.css"));
            $pdfFilePath = 'Profile Info PDF';
            try{
                CommonFunction::pdfGeneration($title, $subject, $stylesheet, $html, $pdfFilePath);
            }catch(Exception $e){
                return back()->with('error','Something Went wrong in pdf generating');
            }
        }else{
            return back()->with('error','Something Went wrong in pdf generating');
        }


    }
    public function groupMembersInfo($tracking_no){
        $tracking_no = Encryption::decodeId($tracking_no);

        $is_pilgrim_profile = (Auth::user()->user_type == '21x101');
        if($is_pilgrim_profile){
                $postData = [
                    'tracking_no' => $tracking_no,
                    'is_child_list' => 1
                ];
                $postdata = http_build_query($postData);
                $base_url = env('API_URL');
                $url = "$base_url/api/get-pilgrim-profile-information";
                $response = PostApiData::getData($url,$postdata);
                $response_data = json_decode($response,true);
                if ($response_data['status'] == 200) {
                    $profileInfo = $response_data['data'];
                }else{
                    return redirect()->back()->with('error',"Something Went Wrong");
                }
        }else{
            return redirect()->back()->with('error',"Pdf profile not found");
        }
        $html = strval( view("ProfileInfoPdf::group_members_info",compact('profileInfo')));
        $title = 'Group Users Info PDF';
        $subject = '';
        $stylesheet= file_get_contents(public_path("assets/stylesheets/group_members_info_pdf.css"));
        $pdfFilePath = 'Group User Info PDF';
        try{
            CommonFunction::pdfGeneration($title, $subject, $stylesheet, $html, $pdfFilePath);

        }catch(Exception $e){
            return back()->with('error','Something Went wrong in pdf generating');
        }
    }
}
