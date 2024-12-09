<?php

namespace App\Modules\News\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Modules\News\Models\News;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\Settings\Models\Configuration;
use App\Modules\Users\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use App\Modules\ProcessPath\Models\ProcessType;

class NewsController extends Controller
{
    public function newsDisplay($id)
    {
        $decodedId = Encryption::decodeId($id);
        if(!$decodedId){
            echo 'Invalid file display request.';
            exit();
        }
        $newsData = News::find($decodedId);
        if(empty($newsData->file_path)){
            echo 'File does not exist.';
            exit();
        }
        $fileData = file_get_contents(public_path($newsData->file_path));
        $extension = explode('.',$newsData->file_path)[1];

        $mime='image/png';
        if($extension == 'pdf'){
            $mime='application/pdf';
        }
        $fileName = $newsData->title.'.'.$extension;
        header("Content-Type: $mime");
        header("Content-Disposition: inline; filename=$fileName");
        echo $fileData;
        exit();
    }
    public function newsList(){

        $news_access_users = Configuration::where('caption','News_and_press')->first();
        $news_access_users_array = json_decode($news_access_users->value2);
        if($news_access_users->value == 0 || !in_array(Auth::user()->user_email,$news_access_users_array)){
            abort('400', 'You have no access right!. Contact with system admin for more information.');
        }
//        if (!ACL::getAccsessRight('user', 'A')) {
//            abort('400', 'You have no access right!. Contact with system admin for more information.');
//        }
        return view('News::list');
    }
    public function getNewsList()
    {
        $userList = News::select()->orderBy('publish_date', 'desc');
        return Datatables::of($userList)
            ->addColumn('action', function ($userList) {
                return '<a href="' . url('newslist/edit/' . Encryption::encodeId($userList->id)) .'" class="btn btn-flat btn-info btn-xs m-1" style="background-color: #009444 ;border-radius:4px"><i class="fa fa-folder-open-o"></i>Open</a>';
            })
            ->editColumn('file_path', function ($userList) {
                $pdfUrl = asset($userList->file_path);
                if (!empty($userList->file_path) && file_exists(public_path($userList->file_path))) {
                    $pdfIcon = '<a href="' . $pdfUrl . '" target="_blank" class="btn btn-flat btn-info btn-xs m-1" style="background-color: #009444 ;border-radius:4px"><i class="fa fa-eye"></i> View</a>';
                } else {
                    $pdfIcon = '';
                }
                return $pdfIcon;
            })
            ->addColumn('publish_date', function ($userList) {
                $carbonDate = Carbon::parse($userList->publish_date);
                return $carbonDate->format('d-M-Y');
            })
            ->rawColumns(['file_path','action','publish_date'])
            ->make(true);
    }
    public function editNewsList($id){
        $news_access_users = Configuration::where('caption','News_and_press')->first();
        $news_access_users_array = json_decode($news_access_users->value2);
        if($news_access_users->value == 0 || !in_array(Auth::user()->user_email,$news_access_users_array)){
            abort('400', 'You have no access right!. Contact with system admin for more information.');
        }
        try {
            $decodeId = Encryption::decodeId($id);
            $userList = News::find($decodeId);
            $post_types = CommonFunction::get_enum_values( 'news', 'post_type' );
            $post_status = CommonFunction::get_enum_values( 'news', 'post_status' );
            return view('News::edit' ,compact(  'userList','post_types', 'post_status') );
        } catch (\Exception $e) {
            Session::flash('error', 'Sorry, something went wrong.');
            return redirect()->back();
        }
    }

    public function updateNewsList(Request $request ,$id){

        if(empty($request['title']) || empty($request['post_type']) || empty($request['post_status'] )|| empty($request['publish_date'])){
            return redirect()->back()->with( Session::flash('error','News related information need to be provided'));
        }
        try {
            DB::beginTransaction();
            $decodedId=Encryption::decodeId($id);
            $appData = News::findOrNew($decodedId);
            $appData->title = $request['title'];
            $appData->description = $request['description'];
            $appData->publish_date = date('Y-m-d H:i:s', strtotime($request['publish_date']));
            $appData->post_type = $request['post_type'];
            $appData->post_status = $request['post_status'];
            if ($request['post_status'] ==  'publish') {
                $appData->status = 1;
            }else{
                $appData->status = 0;
            }

            if ($request->hasFile('file')) {
                $path = 'news/uploads/';
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }
                $uploadedFile = $request->file('file');
                $maxFileSize = 4 * 1024;
                $fileSizeByte = $request->file('file')->getSize();
                $fileSize = $fileSizeByte /1024 ;
                if ($fileSize > $maxFileSize) {
                    return redirect()->back()->with('error', 'The file size must be less than or equal to 4 MB.');
                }
                // Check the file extension
                $allowedExtensions = ['jpg', 'png', 'pdf'];
                $fileExtension = strtolower($uploadedFile->getClientOriginalExtension());
                if (!in_array($fileExtension, $allowedExtensions)) {
                    return redirect()->back()->with('error', 'Invalid file extension. Allowed extensions are JPG, PNG, and PDF.');
                }
                $originalFileName = $uploadedFile->getClientOriginalName();
                $extension = $uploadedFile->getClientOriginalExtension();
                $newFileName = uniqid() . str_replace(' ', '', microtime()) . '.' . $extension;
                $fileContent = file_get_contents($uploadedFile->getRealPath());
                if (file_put_contents($path . $newFileName, $fileContent)) {
                    $appData->file_path = $path . $newFileName;
                }
            }

            $appData->save();
            DB::commit();
            return redirect('/newslist')->with(Session::flash('success','News Update Successfully '));
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('error', 'Sorry, something went wrong. ' . CommonFunction::showErrorPublic($e->getMessage()) . '[PPC-1004]');
            return redirect()->back();
        }
    }
}
