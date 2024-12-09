<?php

namespace App\Modules\Settings\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LoginController;
use App\Libraries\ACL;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Modules\Apps\Models\IndustryCategories;
use App\Modules\CompanyAssociation\Models\CompanyAssociationMaster;
use App\Modules\ProcessPath\Models\ProcessHistory;
use App\Modules\CompanyProfile\Models\CompanyInfo;
use App\Modules\ProcessPath\Models\ProcessPath;
use App\Modules\ProcessPath\Models\ProcessStatus;
use App\Modules\ProcessPath\Models\UserDesk;
use App\Modules\Settings\Models\ApplicationGuideline;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\ProcessPath\Models\ProcessType;
use App\Modules\Settings\Models\ApplicationGuidelineDetails;
use App\Modules\Settings\Models\ApplicationGuidelineDoc;
use App\Modules\Settings\Models\ApplicationRollback;
use App\Modules\Settings\Models\Area;
use App\Modules\Settings\Models\Bank;
use App\Modules\Settings\Models\BankBranch;
use App\Modules\Settings\Models\Currencies;
use App\Modules\Settings\Models\DocInfo;
use App\Modules\Settings\Models\EmailQueue;
use App\Modules\Settings\Models\HighComissions;
use App\Modules\Settings\Models\HomePageSlider;
use App\Modules\Settings\Models\HsCodes;
use App\Modules\Settings\Models\IndustrialCityList;
use App\Modules\Settings\Models\Logo;
use App\Modules\Settings\Models\MaintenanceModeUser;
use App\Modules\Settings\Models\NeedHelp;
use App\Modules\Settings\Models\Notice;
use App\Modules\Settings\Models\PdfPrintRequestQueue;
use App\Modules\Settings\Models\Ports;
use App\Modules\Settings\Models\SecurityProfile;
use App\Modules\Settings\Models\ServiceDetails;
use App\Modules\Settings\Models\TermsCondition;
use App\Modules\Settings\Models\Units;
use App\Modules\Settings\Models\UserManual;
use App\Modules\Settings\Models\WhatsNew;
use App\Modules\Settings\Models\HomeContent;
use App\Modules\Users\Models\Countries;
use App\Modules\Users\Models\EconomicZones;
use App\Modules\Users\Models\ParkInfo;
use App\Modules\Users\Models\Users;
use App\Modules\Users\Models\UserTypes;
use App\Modules\Web\Models\HomePageArticle;
use App\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Mockery\Exception;
use Session;
use Yajra\DataTables\DataTables;

class SettingsControllerV2 extends Controller
{
    public function index()
    {
        return view("Settings::index");
    }

    public function BankListv2(Request $request)
    {
        $search_input = $request->get('search');
        $limit = $request->get('limit');
        $query = Bank::orderBy('id');
        if ($search_input) {
            $query->where(function ($query) use ($search_input) {
                $query->where('name', 'like', '%' . $search_input . '%')
                    ->orWhere('email', 'like', '%' . $search_input . '%');
            });
        }
        $data = $query->paginate($limit);
        $data->getCollection()->transform(function ($bank, $key) {
            return [
                'si' => $key + 1, 'id' => Encryption::encodeId($bank->id), 'name' => $bank->name, 'bank_code' => $bank->bank_code,
                'location' => $bank->location, 'email' => $bank->email, 'phone' => $bank->phone, 'is_active' => $bank->is_active,
            ];
        });

        return response()->json($data);
    }
    //    bank list


    //for view js

    public function bankv2()
    {
        if (!ACL::getAccsessRight('settings', 'V')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $getList = Bank::where("is_archive", 0)->get();
        return view("Settings::bank_2.list", compact('getList'));
    }

    public function editBankv2($bank_id)
    {
        $bank_id = Encryption::decodeId($bank_id);
        $data = Bank::where('id', $bank_id)->first();
        return $data;
    }

    public function updateBankv2(Request $request, $id)
    {

        if (!ACL::getAccsessRight('settings', 'E')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $bank_id = Encryption::decodeId($id);
        $this->validate($request, [
            'name' => 'required',
            'bank_code' => 'required',
            'location' => 'required',
            'email' => 'required|email',
            'phone' => 'required|Max:50|regex:/[0-9+,-]$/',
        ]);
        try {

            $bank = Bank::find($bank_id);
            $bank->name = $request->get('name');
            $bank->bank_code = $request->get('bank_code');
            $bank->location = $request->get('location');
            $bank->email = $request->get('email');
            $bank->phone = $request->get('phone');
            $bank->address = $request->get('address');
            $bank->website = $request->get('website');
            $bank->is_active = $request->get('is_active');
            $bank->save();

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            dd($request->all());
            return response()->json(['status' => false]);
        }
    }

    public function storeBankv2(Request $request)
    {
        //
        if (!ACL::getAccsessRight('settings', 'A')) {
            die('You have no access right! Please contact system administration for more information.');
        }

        $this->validate($request, [
            'name' => 'required',
            'code' => 'required',
            'location' => 'required',
            'email' => 'required|email',
            'phone' => 'required|Max:50|regex:/[0-9+,-]$/',
        ]);

        try {
            $insert = Bank::create(
                array(
                    'name' => $request->get('name'),
                    'bank_code' => $request->get('code'),
                    'location' => $request->get('location'),
                    'email' => $request->get('email'),
                    'phone' => $request->get('phone'),
                    'address' => $request->get('address'),
                    'website' => $request->get('website'),
                    'created_by' => CommonFunction::getUserId()
                )
            );
            return response()->json(['status' => true]);

            //            Session::flash('success', 'Data is stored successfully!');
            //            return redirect('/settings/edit-bank/' . Encryption::encodeId($insert->id));
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }

    public function branch()
    {
        if (!ACL::getAccsessRight('settings', 'V')) {
            abort('400', 'You have no access right! Please contact system administration for more information.');
        }
        $data = BankBranch::leftJoin('bank', 'bank.id', '=', 'bank_branches.bank_id')
            ->first(['bank_branches.*', 'bank.name as bank_name']);
        return view("Settings::branch.list", compact('data'));
    }

    public function BranchListv2(Request $request)
    {
        $search_input = $request->get('search');
        $limit = $request->get('limit');
        $order = $request->get('order');
        $column_name = $request->get('column_name');
        $query = BankBranch::leftJoin('bank', 'bank.id', '=', 'bank_branches.bank_id')
            ->select('bank_branches.*', 'bank.name as bank_name');
        if ($search_input) {
            $query->where(function ($query) use ($search_input) {
                $query->where('branch_name', 'like', '%' . $search_input . '%')
                    ->orWhere('bank_branches.address', 'like', '%' . $search_input . '%')
                    ->orWhere('bank.name', 'like', '%' . $search_input . '%');
            });
        }
        if ($column_name) {
            $query->orderBy($column_name, $order);
        }
        $data = $query->paginate($limit);
        $data->getCollection()->transform(function ($branch, $key) {
            return [
                'si' => $key + 1, 'id' => Encryption::encodeId($branch->id), 'branch_name' => $branch->branch_name,
                'branch_code' => $branch->branch_code, 'bank_name' => $branch->bank_name, 'address' => $branch->address, 'manager_info' => $branch->manager_info, 'is_active' => $branch->is_active,
            ];
        });


        return response()->json($data);
    }

    //    ....................Bank end....................
    public function bankName(Request $request)
    {
        $banks = Bank::orderBy('name')
            ->where('is_active', 1)->get();
        return response()->json($banks);
    }

    public function storeBranch(Request $request)
    {

        if (!ACL::getAccsessRight('settings', 'A')) {
            die('You have no access right! Please contact system administration for more information.');
        }

        $this->validate($request, [
            'branch_code' => 'required',
            'branch_name' => 'required',
            'address' => 'required',
        ]);
        try {
            $insert = BankBranch::create(
                array(
                    'bank_id' => $request->get('bank_id'),
                    'branch_code' => $request->get('branch_code'),
                    'branch_name' => $request->get('branch_name'),
                    'address' => $request->get('address'),
                    'manager_info' => $request->get('manager_info'),
                    'created_by' => CommonFunction::getUserId()
                )
            );
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            dd($e->getMessage());
            return response()->json(['status' => false]);
        }
    }

    public function editBranch($branch_id)
    {
        $branch_id = Encryption::decodeId($branch_id);
        $data = BankBranch::where('id', $branch_id)->first();
        return $data;
    }

    public function storeAndUpdateBranch(Request $request, $id)
    {

        if (!ACL::getAccsessRight('settings', 'E')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $branch_id = Encryption::decodeId($id);
        $this->validate($request, [
            'branch_code' => 'required',
            'branch_name' => 'required',
            'address' => 'required',
        ]);
        try {
            $branch = BankBranch::find($branch_id);
            $branch->bank_id = $request->get('bank_id');
            $branch->branch_code = $request->get('branch_code');
            $branch->branch_name = $request->get('branch_name');
            $branch->address = $request->get('address');
            $branch->manager_info = $request->get('manager_info');
            $branch->is_active = $request->get('is_active');
            $branch->save();

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            dd($request->all());
            return response()->json(['status' => false]);
        }
    }

    //    ..........Branch end...................
    public function NoticeList(Request $request)
    {
        $search_input = $request->get('search');
        $limit = $request->get('limit');
        $query = Notice::orderBy('id');
        if ($search_input) {
            $query->where(function ($query) use ($search_input) {
                $query->where('heading', 'like', '%' . $search_input . '%')
                    ->orWhere('details', 'like', '%' . $search_input . '%')
                    ->orWhere('importance', 'like', '%' . $search_input . '%')
                    ->orWhere('status', 'like', '%' . $search_input . '%');
            });
        }
        $data = $query->paginate($limit);
        $data->getCollection()->transform(function ($notice, $key) {
            return [
                'si' => $key + 1, 'id' => Encryption::encodeId($notice->id), 'heading' => $notice->heading, 'details' => $notice->details,
                'importance' => $notice->importance, 'status' => $notice->status,
            ];
        });

        return response()->json($data);
    }

    public function storeNotice(Request $request)
    {
        if (!ACL::getAccsessRight('settings', 'A')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $messages = [
            'heading.required' => 'Heading (Bangla) field is required',
            'heading_en.required' => 'Heading (English) field is required',
            'details.required' => 'Details (Bangla) field is required',
            'details_en.required' => 'Details (English) field is required',
        ];

        $rules = [
            'heading' => 'required',
            'heading_en' => 'required',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'details' => 'required',
            'details_en' => 'required',
            'importance' => 'required',
            'ordering_prefix' => 'required',
            'status' => 'required',
        ];

        $this->validate($request, $rules, $messages);
        try {
            $notice_photo = $request->file('photo');
            $notice_photo_path = '';
            $path = 'uploads/Notice/';
            if ($request->hasFile('photo')) {
                $file_name = 'notice_image_' . md5(uniqid()) . '.' . $notice_photo->getClientOriginalExtension();
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $notice_photo->move($path, $file_name);
                $notice_photo_path = $path . $file_name;
            }

            $notice_document = $request->file('notice_document');
            $notice_document_path = '';
            if ($request->hasFile('notice_document')) {
                $file_name = 'notice_doc_' . md5(uniqid()) . '.' . $notice_document->getClientOriginalExtension();
                $file_size =  number_format($notice_document->getSize() / 1048576, 2);
                if ($file_size > 2) {
                    return response()->json(['status' => false, 'messages' => "The Document size should be maximum 2MB"]);
                }

                $mime_type = $notice_document->getClientMimeType();

                if ($mime_type == 'application/pdf' || $mime_type == 'application/octet-stream') {
                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }
                    $notice_document->move($path, $file_name);
                    $notice_document_path = $path . $file_name;
                } else {
                    return response()->json(['status' => false, 'messages' => "The Document must be pdf type"]);
                }
            }

            Notice::create(
                array(
                    'heading' => $request->get('heading'),
                    'heading_en' => $request->get('heading_en'),
                    'photo' => $notice_photo_path,
                    'notice_document' => $notice_document_path,
                    'details' => $request->get('details'),
                    'details_en' => $request->get('details_en'),
                    'status' => $request->get('status'),
                    'importance' => $request->get('importance'),
                    'ordering_prefix' => $request->get('ordering_prefix'),
                    'created_by' => CommonFunction::getUserId()
                )
            );
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }

    public function editNotice($notice_id)
    {
        $notice_id = Encryption::decodeId($notice_id);
        return Notice::where('id', $notice_id)->first();
    }

    public function updateNotice($id, Request $request)
    {
        if (!ACL::getAccsessRight('settings', 'E')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $notice_id = Encryption::decodeId($id);

        $messages = [
            'heading.required' => 'Heading (Bangla) field is required',
            'heading_en.required' => 'Heading (English) field is required',
            'details.required' => 'Details (Bangla) field is required',
            'details_en.required' => 'Details (English) field is required',
        ];

        $rules = [
            'heading' => 'required',
            'heading_en' => 'required',
            'details' => 'required',
            'details_en' => 'required',
            'importance' => 'required',
            'ordering_prefix' => 'required',
            'status' => 'required',
        ];
        if ($request->hasFile('photo')) {
            $rules['photo'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:1024';
        }
        $this->validate($request, $rules, $messages);

        try {
            $notice_photo = $request->file('photo');
            $notice_photo_path = '';
            $path = 'uploads/Notice/';
            if ($request->hasFile('photo')) {
                $file_name = 'notice_image_' . md5(uniqid()) . '.' . $notice_photo->getClientOriginalExtension();
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $notice_photo->move($path, $file_name);
                $notice_photo_path = $path . $file_name;
            }

            $notice_document = $request->file('notice_document');
            $notice_document_path = '';
            if ($request->hasFile('notice_document')) {
                $file_name = 'notice_doc_' . md5(uniqid()) . '.' . $notice_document->getClientOriginalExtension();
                $file_size =  number_format($notice_document->getSize() / 1048576, 2);
                if ($file_size > 2) {
                    return response()->json(['status' => false, 'messages' => "The Document size should be maximum 2MB"]);
                }

                $mime_type = $notice_document->getClientMimeType();

                if ($mime_type == 'application/pdf' || $mime_type == 'application/octet-stream') {
                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }
                    $notice_document->move($path, $file_name);
                    $notice_document_path = $path . $file_name;
                } else {
                    return response()->json(['status' => false, 'messages' => "The Document must be pdf type"]);
                }
            }

            $notice = Notice::find($notice_id);
            $notice->heading = $request->get('heading');
            $notice->heading_en = $request->get('heading_en');
            if (!empty($notice_photo_path)) {
                $notice->photo = $notice_photo_path;
            }
            if (!empty($notice_document_path)) {
                $notice->notice_document = $notice_document_path;
            }
            $notice->details = $request->get('details');
            $notice->details_en = $request->get('details_en');
            $notice->importance = $request->get('importance');
            $notice->ordering_prefix = $request->get('ordering_prefix');
            $notice->status = $request->get('status');
            $notice->save();

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            dd($request->all());
            return response()->json(['status' => false]);
        }
    }

    public function storeCurrency(Request $request)
    {
        if (!ACL::getAccsessRight('settings', 'A')) {
            die('You have no access right! Please contact system administration for more information.');
        }

        $this->validate($request, [
            'code' => 'required',
            'name' => 'required',
            'usd_value' => '',
            'bdt_value' => '',
        ]);
        try {
            $insert = Currencies::create([
                'code' => $request->get('code'),
                'name' => $request->get('name'),
                'usd_value' => $request->get('usd_value'),
                'bdt_value' => $request->get('bdt_value'),
                'created_by' => CommonFunction::getUserId(),
            ]);
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }

    //    ........notice end...............................
    public function CurrencyList(Request $request)
    {

        $search_input = $request->get('search');
        $limit = $request->get('limit');
        $query = Currencies::orderBy('code')->where('is_archive', 0);
        if ($search_input) {
            $query->where(function ($query) use ($search_input) {
                $query->where('code', 'like', '%' . $search_input . '%')
                    ->orWhere('name', 'like', '%' . $search_input . '%')
                    ->orWhere('usd_value', 'like', '%' . $search_input . '%')
                    ->orWhere('bdt_value', 'like', '%' . $search_input . '%');
            });
        }
        $data = $query->paginate($limit);
        $data->getCollection()->transform(function ($currency, $key) {
            return [
                'si' => $key + 1, 'id' => Encryption::encodeId($currency->id), 'code' => $currency->code, 'name' => $currency->name,
                'usd_value' => $currency->usd_value, 'bdt_value' => $currency->bdt_value, 'is_active' => $currency->is_active,
            ];
        });

        return response()->json($data);
    }

    public function editCurrency($encrypted_id)
    {
        $id = Encryption::decodeId($encrypted_id);
        $data = Currencies::where('id', $id)->first();
        return $data;
    }

    public function updateCurrency($enc_id, Request $request)
    {
        if (!ACL::getAccsessRight('settings', 'E')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $id = Encryption::decodeId($enc_id);

        $this->validate($request, [
            'code' => 'required',
            'name' => 'required',
            'usd_value' => '',
            'bdt_value' => '',
        ]);
        try {
            Currencies::where('id', $id)->update([
                'code' => $request->get('code'),
                'name' => $request->get('name'),
                'usd_value' => $request->get('usd_value'),
                'bdt_value' => $request->get('bdt_value'),
                'is_active' => $request->get('is_active'),
                'updated_by' => CommonFunction::getUserId()
            ]);

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            dd($request->all());
            return response()->json(['status' => false]);
        }
    }

    public function serviceName(Request $request)
    {
        $services = ProcessType::orderby('name')->get();
        return response()->json($services);
    }

    public function storeDocument(Request $request)
    {
        if (!ACL::getAccsessRight('settings', 'A')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $this->validate($request, [
            'doc_name' => 'required',
            'process_type_id' => 'required',
        ]);
        try {
            $insert = docInfo::create([
                'doc_name' => $request->get('doc_name'),
                'process_type_id' => $request->get('process_type_id'),
                'doc_priority' => $request->get('doc_priority'),
                'created_by' => CommonFunction::getUserId()
            ]);
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }

    public function DocumentList(Request $request)
    {
        $search_input = $request->get('search');
        $limit = $request->get('limit');
        $query = docInfo::leftJoin('process_type', 'process_type.id', '=', 'doc_info.process_type_id')
            ->select('doc_info.*', 'process_type.name as process_name');
        if ($search_input) {
            $query->where(function ($query) use ($search_input) {
                $query->where('doc_name', 'like', '%' . $search_input . '%')
                    ->orWhere('process_type.name', 'like', '%' . $search_input . '%')
                    ->orWhere('doc_priority', 'like', '%' . $search_input . '%');
            });
        }
        $data = $query->paginate($limit);
        $data->getCollection()->transform(function ($document, $key) {
            return [
                'si' => $key + 1, 'id' => Encryption::encodeId($document->id), 'doc_name' => $document->doc_name, 'process_name' => $document->process_name,
                'doc_priority' => $document->doc_priority,
            ];
        });

        return response()->json($data);
    }

    public function editDocument($id)
    {
        if (!ACL::getAccsessRight('settings', 'E')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $_id = Encryption::decodeId($id);
        $data = docInfo::where('id', $_id)->first();
        return $data;
    }

    public function updateDocument($id, Request $request)
    {
        if (!ACL::getAccsessRight('settings', 'E')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $_id = Encryption::decodeId($id);

        $this->validate($request, [
            'doc_name' => 'required',
            'process_type_id' => 'required',
        ]);
        try {
            docInfo::where('id', $_id)->update([
                'doc_name' => $request->get('doc_name'),
                'process_type_id' => $request->get('process_type_id'),
                'doc_priority' => $request->get('doc_priority'),
                'updated_by' => CommonFunction::getUserId()
            ]);
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }


    public function storeArea(Request $request)
    {
        if (!ACL::getAccsessRight('settings', 'A')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $this->validate($request, [
            'area_nm' => 'required',
            'area_nm_ban' => 'required',
        ]);
        try {
            $area_type = $request->get('area_type');
            if ($area_type == 1) { //for division
                $parent_id = 0;
            } elseif ($area_type == 2) { // for district
                $parent_id = $request->get('division');
            } elseif ($area_type == 3) { //for thana
                $parent_id = $request->get('district');
            }

            $insert = Area::create([
                'area_type' => $area_type,
                'pare_id' => $parent_id,
                'area_nm' => $request->get('area_nm'),
                'area_nm_ban' => $request->get('area_nm_ban'),
            ]);

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }

    public function AreaList(Request $request)
    {

        if (!ACL::getAccsessRight('settings', 'V'))
            die('You have no access right! Please contact system administration for more information.');

        $search_input = $request->get('search');
        $limit = $request->get('limit');
        $order = $request->get('order');
        $column_name = $request->get('column_name');
        $query = Area::select('area_info.*');

        if ($search_input) {
            $query->where(function ($query) use ($search_input) {
                $query->where('area_nm', 'like', '%' . $search_input . '%')
                    ->orWhere('area_nm_ban', 'like', '%' . $search_input . '%')
                    ->orWhere('area_type', 'like', '%' . $search_input . '%');
            });
        }
        if ($column_name) {
            $query->orderBy($column_name, $order);
        }
        $data = $query->paginate($limit);
        $data->getCollection()->transform(function ($area, $key) {
            return [
                'si' => $key + 1, 'id' => Encryption::encodeId($area->area_id), 'area_nm' => $area->area_nm, 'area_nm_ban' => $area->area_nm_ban,
                'area_type' => $area->area_type,
            ];
        });

        return response()->json($data);
    }

    public function divisionName(Request $request)
    {
        $divisions = Area::orderBy('area_nm')->where('area_type', 1)->get();

        return response()->json($divisions);
    }

    public function districtName(Request $request)
    {
        $divisionId = $request->get('division');

        $districts = Area::orderBy('area_nm')->where('pare_id', $divisionId)->get();
        return response()->json($districts);
    }

    public function editArea($id)
    {

        $area_id = Encryption::decodeId($id);
        $data = Area::leftJoin('area_info as ai', 'area_info.pare_id', '=', 'ai.area_id')
            ->where('area_info.area_id', $area_id)
            ->first(['area_info.*', 'ai.pare_id as division_id']);
        return $data;
    }

    public function updateArea($id, Request $request)
    {

        if (!ACL::getAccsessRight('settings', 'E')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $area_id = Encryption::decodeId($id);
        $this->validate($request, [
            'area_nm' => 'required',
            'area_nm_ban' => 'required',
        ]);
        try {
            $area_type = $request->get('area_type');
            if ($area_type == 1) { //for division
                $parent_id = 0;
            } elseif ($area_type == 2) { // for district
                $parent_id = $request->get('division');
            } elseif ($area_type == 3) { //for thana
                $parent_id = $request->get('district');
            }

            Area::where('area_id', $area_id)->update([
                'area_type' => $area_type,
                'pare_id' => $parent_id,
                'area_nm' => $request->get('area_nm'),
                'area_nm_ban' => $request->get('area_nm_ban'),
            ]);
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }

    public function get_thana_by_district_id(Request $request)
    {
        $district_id = $request->get('districtId');
        $thanas = Area::where('PARE_ID', $district_id)->orderBy('AREA_NM', 'ASC')->pluck('AREA_NM', 'AREA_ID')->toArray();
        $data = ['responseCode' => 1, 'data' => $thanas];
        return response()->json($data);
    }

    public function StoreTermsCondition(Request $request)
    {
        if (!ACL::getAccsessRight('settings', 'A')) {
            die('You have no access right! Please contact system administration for more information.');
        }

        $this->validate($request, [
            'subject' => 'required',
            'showing_order' => 'integer',
            'status' => 'required|boolean'
        ]);

        try {
            $pdfFile = $request->file('file_data');
            $filepath = '';
            $path = "uploads/TermsCondition/";
            if ($request->hasFile('file_data')) {
                $pdf_file = $pdfFile->getClientOriginalName();
                $mime_type = $pdfFile->getClientMimeType();
                if ($mime_type == 'application/pdf' || $mime_type == 'application/octet-stream') {
                    $pdfFile->move($path, $pdf_file);
                    $filepath = $path . $pdf_file;
                } else {
                    return response()->json(['status' => false, 'messages' => "File must be pdf format"]);
                }
            }
            $data = new TermsCondition();
            $data->subject = $request->get('subject');
            $data->description = $request->get('description');
            $data->showing_order = $request->get('showing_order');
            $data->pdf_link = $filepath;
            $data->status = $request->get('status');
            $data->save();
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            dd($request->all());
            return response()->json(['status' => false]);
        }
    }

    public function TermsConditionList(Request $request)
    {

        $search_input = $request->get('search');
        $limit = $request->get('limit');
        $query = TermsCondition::orderBy('showing_order', 'asc');

        if ($search_input) {
            $query->where(function ($query) use ($search_input) {
                $query->where('subject', 'like', '%' . $search_input . '%')
                    ->orWhere('showing_order', 'like', '%' . $search_input . '%')
                    ->orWhere('status', 'like', '%' . $search_input . '%');
            });
        }
        $data = $query->paginate($limit);
        $data->getCollection()->transform(function ($termscondition, $key) {
            return [
                'si' => $key + 1, 'id' => Encryption::encodeId($termscondition->id), 'subject' => $termscondition->subject, 'showing_order' => $termscondition->showing_order,
                'description' => $termscondition->description, 'status' => $termscondition->status,
            ];
        });

        return response()->json($data);
    }

    public function editTermsCondition($encrypted_id)
    {

        $id = Encryption::decodeId($encrypted_id);
        $data = TermsCondition::where('id', $id)->first();
        return $data;
    }

    public function updateTermsCondition(Request $request)
    {
        $decodedId = Encryption::decodeId($request->id);
        if (!ACL::getAccsessRight('settings', 'E')) {
            die('You have no access right! Please contact system administration for more information.');
        }

        $this->validate($request, [
            'subject' => 'required',
            'showing_order' => 'integer',
            //            'files' => 'required',
            'status' => 'required|boolean'
        ]);

        try {
            $roules = TermsCondition::Where('id', $decodedId)->first();
            $pdfFile = $request->file('file_data');

            $path = "uploads/TermsCondition/";
            if ($request->hasFile('file_data')) {
                $pdf_file = $pdfFile->getClientOriginalName();
                $mime_type = $pdfFile->getClientMimeType();
                if ($mime_type == 'application/pdf' || $mime_type == 'application/octet-stream') {
                    $pdfFile->move($path, $pdf_file);
                    $filepath = $path . $pdf_file;
                    $roules->pdf_link = $filepath;
                } else {
                    return response()->json(['status' => false, 'messages' => "File must be pdf format"]);
                }
            }

            $roules->subject = $request->get('subject');
            $roules->description = $request->get('description');
            $roules->showing_order = $request->get('showing_order');
            $roules->status = $request->get('status');
            $roules->save();
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }

    /**************** Start of Company Info functions *********/
    public function CompanyInfoList(Request $request)
    {
        $search_input = $request->get('search');
        $limit = $request->get('limit');
        $companyInformation = CompanyInfo::leftJoin('users as user', 'user.id', '=', 'company_info.created_by')
            ->orderBy('created_at', 'desc')
            ->select('company_info.*', DB::raw("CONCAT('users.user_first_name ','users.user_middle_name ', 'user_last_name') AS user_full_name"), 'user.user_email');

        if ($search_input) {
            $companyInformation->where(function ($companyInformation) use ($search_input) {
                $companyInformation->where('org_nm', 'like', '%' . $search_input . '%');
            });
        }
        $data = $companyInformation->paginate($limit);
        $data->getCollection()->transform(function ($company, $key) {
            return [
                'si' => $key + 1, 'id' => Encryption::encodeId($company->id), 'org_nm' => $company->org_nm, 'org_nm_bn' => $company->org_nm_bn, 'company_status' => $company->company_status, 'is_rejected' => $company->is_rejected, 'is_approved' => $company->is_approved, 'user_email'=> $company->user_email,'created_at' => date('d-M-Y', strtotime($company->created_at)),
            ];
        });

        return response()->json($data);
    }

    public function CountryName(Request $request)
    {
        $country = Countries::orderby('nicename')->get();
        return response()->json($country);
    }

    public function StoreCompanyInfo(request $request)
    {
        //        dd(123);
        if (!ACL::getAccsessRight('settings', 'A')) {
            abort('400', 'You have no access right! Please contact system administration for more information.');
        }
        $this->validate($request, [
            'company_name' => 'required|unique:company_info',
            'country_id' => 'required',
            'state' => 'required_unless:country_id,18',
            'province' => 'required_unless:country_id,18',
            'division' => 'required_if:country_id,18',
            'district' => 'required_if:country_id,18',
            'thana' => 'required_if:country_id,18'
        ]);

        try {
            $companyData = new CompanyInfo();
            $companyData->company_name = $request->get('company_name');
            $companyData->country_id = $request->get('country_id');
            $companyData->state = $request->get('state');
            $companyData->state = $request->get('state');
            $companyData->province = $request->get('province');
            $companyData->division = $request->get('division');
            $companyData->district = $request->get('district');
            $companyData->thana = $request->get('thana');
            $companyData->save();

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }

    public function companyInfoAction($id)
    {
        $company_id = Encryption::decodeId($id);
        $data = CompanyInfo::leftJoin('country_info as country', 'country.id', '=', 'company_info.country_id')
            ->leftJoin('area_info as division', 'division.area_id', '=', 'company_info.division')
            ->leftJoin('area_info as district', 'district.area_id', '=', 'company_info.district')
            ->leftJoin('area_info as thana', 'thana.area_id', '=', 'company_info.thana')
            ->leftJoin('users as user', 'user.id', '=', 'company_info.created_by')
            ->select(
                'company_info.*',
                'country.nicename as country',
                'division.area_nm as company_division',
                'district.area_nm as company_district',
                'thana.area_nm as company_thana',
                DB::raw('CONCAT(user.user_first_name, " ", user.user_middle_name," ", user.user_last_name) AS user_full_name'),
                DB::raw('CONCAT(company_name, ", ", division.area_nm,", ", thana.area_nm) AS company_info')
            )
            ->where('company_info.id', $company_id)
            ->first();
        return response()->json($data);
    }

    private function logoutAllUserByCompanyId($company_id)
    {
        $current_login_users = User::where('working_company_id', $company_id)
            ->where('login_token', '<>', '')
            ->get([
                'id',
                'login_token'
            ]);
        if ($current_login_users->isNotEmpty()) {
            foreach ($current_login_users as $user) {
                $sessionID = Encryption::decode($user->login_token);
                Session::getHandler()->destroy($sessionID);
                Users::where('id', $user->id)->update(['login_token' => '']);
            }
        }
    }

    public function companyChangeStatus($id, $status_id)
    {
        try {
            DB::beginTransaction();

            $company_id = Encryption::decodeId($id);
            $companyData = CompanyInfo::find($company_id);
            $companyData->company_status = $status_id;
            $companyData->save();

            CompanyAssociationMaster::where('company_id', $company_id)->update(['is_active' => $status_id]);

            DB::commit();

            return response()->json(['status'=>true]);
        } catch (Exception $e) {
            DB::rollback();
            return response()->json(['status'=>false]);
        }
    }
    /**************** End of Rejected Info functions *********/
    /**************** Start of UserType functions *********/
    public function userTypeList(Request $request)
    {

        $search_input = $request->get('search');
        $limit = $request->get('limit');
        $user_types = UserTypes::leftJoin('security_profile', 'security_profile.id', '=', 'user_types.security_profile_id')
            ->select('user_types.id', 'user_types.id as type_id', 'type_name', 'security_profile_id', 'profile_name', 'week_off_days', 'user_types.status');
        if ($search_input) {
            $user_types->where(function ($user_types) use ($search_input) {
                $user_types->where('type_name', 'like', '%' . $search_input . '%');
            });
        }
        $data = $user_types->paginate($limit);
        $data->getCollection()->transform(function ($usertype, $key) {
            return [
                'si' => $key + 1, 'id' => Encryption::encode($usertype->type_id), 'type_name' => $usertype->type_name,
                'type_id' => $usertype->type_id,
                'profile_name' => $usertype->profile_name, 'week_off_days' => $usertype->week_off_days, 'status' => $usertype->status,
            ];
        });
        return response()->json($data);
    }


    public function editUserType($id)
    {
        $id = Encryption::decode($id);
        //        dd($id);
        $data = UserTypes::where('id', $id)->first();
        return response()->json($data);
    }
    public function getSecurityList()
    {
        $data = SecurityProfile::where('active_status', 'yes')->get();
        return response()->json($data);
    }

    public function updateUserType(Request $request, $id)
    {
        if (!ACL::getAccsessRight('settings', 'A')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $this->validate($request, [
            'type_name' => 'required',
            'security_profile_id' => 'required',
            'auth_token_type' => 'required',
            'status' => 'required',
        ]);
        //        CommonFunction::createAuditLog('userType.edit', $request);
        $id = Encryption::decode($id);
        //        dd($id);
        try {
            $update_data = array(
                'type_name' => $request->get('type_name'),
                'security_profile_id' => $request->get('security_profile_id'),
                'auth_token_type' => $request->get('auth_token_type'),
                'db_access_data' => Encryption::encode($request->get('db_access_data')),
                'status' => $request->get('status')
            );
            UserTypes::where('id', $id)
                ->update($update_data);

            if ($request->get('status') == 'inactive') {
                $user_ids = UsersModel::where('user_type', $id)->get(['id']);
                foreach ($user_ids as $user_id) {
                    LoginController::killUserSession($user_id);
                }
            }
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }

    /**************** End of UserType functions *********/


    /**************** Start of Home page slider related functions *********/
    public function HomePageSliderList(Request $request)
    {

        $search_input = $request->get('search');
        $limit = $request->get('limit');
        $query = HomePageSlider::orderBy('id', 'desc');
        if ($search_input) {
            $query->where(function ($query) use ($search_input) {
                $query->where('slider_url', 'like', '%' . $search_input . '%');
            });
        }
        $data = $query->paginate($limit);
        $data->getCollection()->transform(function ($silder, $key) {
            return [
                'si' => $key + 1, 'id' => Encryption::encodeId($silder->id), 'slider_image' => $silder->slider_image,
                'slider_title' => $silder->slider_title, 'status' => $silder->status, 'slider_order' => $silder->slider_order
            ];
        });

        return response()->json($data);
    }

    public function homePageSliderStore(Request $request)
    {
        //        dd($request->all());
        if (!ACL::getAccsessRight('settings', 'A')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $this->validate($request, [
            'status' => 'required',
            'slider_order' => 'required|numeric|unique:home_page_slider,slider_order'
        ]);
        try {

            $image = $request->file('slider_image');
            //            dd($image);
            // $path = "uploads/sliderImage";
            $path = "news/uploads/sliderImage";
            if ($request->hasFile('slider_image')) {
                $img_file = $image->getClientOriginalName();
                $mime_type = $image->getClientMimeType();
                if ($mime_type == 'image/jpeg' || $mime_type == 'image/jpg' || $mime_type == 'image/png' || $mime_type == 'image/webp') {
                    $image->move($path, $img_file);
                    $filepath = $path . '/' . $img_file;
                } else {
                    return response()->json(['status' => false, 'messages' => "Image must be png or jpg or jpeg format"]);
                }
            }
            $insert = HomePageSlider::create(
                array(
                    'slider_title' => $request->get('slider_title'),
                    'slider_url' => $request->get('slider_url'),
                    'slider_type' => $request->get('slider_type'),
                    'status' => $request->get('status'),
                    'slider_image' => $filepath,
                    'created_by' => CommonFunction::getUserId(),
                    'slider_order' => $request->get('slider_order'),
                )
            );

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false , 'messages'=>$e->getMessage() ]);
        }
    }

    public function editHomePageSlider($encrypted_id)
    {
        $id = Encryption::decodeId($encrypted_id);
        $data = HomePageSlider::where('id', $id)->first();
        //        $page_header = 'Slider';
        return $data;
    }

    public function updateHomePageSlider(Request $request)
    {
        if (!ACL::getAccsessRight('settings', 'E')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $id = Encryption::decodeId($request->id);

        $this->validate($request, [
            'status' => 'required',
            'slider_order' => 'required|numeric|unique:home_page_slider,slider_order,'.$id,
        ]);
        try {
            $slider = HomePageSlider::Where('id', $id)->first();
            $image = $request->file('slider_image');
            // $path = "uploads/sliderImage";
            $path = "news/uploads/sliderImage";

            if ($request->hasFile('slider_image') && !empty($request->slider_image)) {
                $img_file = $image->getClientOriginalName();
                $mime_type = $image->getClientMimeType();
                if ($mime_type == 'image/jpeg' || $mime_type == 'image/jpg' || $mime_type == 'image/png' || $mime_type == 'image/webp') {
                    $image->move($path, $img_file);
                    $filepath = $path . '/' . $img_file;
                    $slider->slider_image = $filepath;
                } else {
                    return response()->json(['status' => false, 'messages' => "Image must be png or jpg or jpeg format"]);
                }
            }

            $slider->slider_title = $request->get('slider_title');
            $slider->slider_url = $request->get('slider_url');
            $slider->status = $request->get('status');
            $slider->created_by = CommonFunction::getUserId();
            $slider->slider_order = $request->get('slider_order');
            $slider->save();
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }
    /**************** Ending of Home page slider related functions *********/

    /**************** Start of User Manual related functions *********/

    public function UserManualList(Request $request)
    {

        $search_input = $request->get('search');
        $limit = $request->get('limit');
        $query = UserManual::orderBy('typeName', 'desc');
        //        dd($query);
        if ($search_input) {
            $query->where(function ($query) use ($search_input) {
                $query->where('typeName', 'like', '%' . $search_input . '%')
                    ->orWhere('termsCondition', 'like', '%' . $search_input . '%');
            });
        }
        $data = $query->paginate($limit);
        $data->getCollection()->transform(function ($usermanual, $key) {
            return [
                'si' => $key + 1, 'id' => Encryption::encodeId($usermanual->id), 'typeName' => $usermanual->typeName,
                'details' => strip_tags($usermanual->details), 'termsCondition' => $usermanual->termsCondition, 'status' => $usermanual->status,
            ];
        });

        return response()->json($data);
    }

    public function UsermanualStore(Request $request)
    {
        // return $request->all();
        if (!ACL::getAccsessRight('settings', 'A')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $this->validate($request, [
            'typeName' => 'required',
            'details' => 'required',
            'termsCondition' => 'required',
            'status' => 'required'
        ]);
        try {
            $pdfFile = $request->file('pdfFile');
            $path = "uploads/userManual/";
            if ($request->hasFile('pdfFile')) {
                $pdf_file = $pdfFile->getClientOriginalName();
                $mime_type = $pdfFile->getClientMimeType();
                if ($mime_type == 'application/pdf' || $mime_type == 'application/octet-stream') {
                    $pdfFile->move($path, $pdf_file);
                    $filepath = $path . $pdf_file;
                } else {
                    return response()->json(['status' => false, 'messages' => "File must be pdf format"]);
                }
            }
            UserManual::create(
                array(
                    'typeName' => $request->get('typeName'),
                    'details' => $request->get('details'),
                    'termsCondition' => $request->get('termsCondition'),
                    'status' => $request->get('status'),
                    'pdfFile' => $filepath,
                    'created_by' => CommonFunction::getUserId()
                )
            );

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }

    public function editUsermanual($encrypted_id)
    {
        $id = Encryption::decodeId($encrypted_id);
        $data = UserManual::where('id', $id)->first();
        //        $pdf = explode("/", $data->pdfFile);
        //        $filepdf = $pdf[2];
        return $data;
    }

    public function updateUsermanual(Request $request)
    {

        if (!ACL::getAccsessRight('settings', 'E')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $id = Encryption::decodeId($request->id);

        $this->validate($request, [
            'typeName' => 'required',
            'details' => 'required',
            'termsCondition' => 'required',
            'status' => 'required'
        ]);
        try {
            $manual = UserManual::Where('id', $id)->first();
            $pdfFile = $request->file('pdfFile');
            $path = "uploads/userManual/";

            if ($request->hasFile('pdfFile')) {
                $pdf_file = $pdfFile->getClientOriginalName();
                $mime_type = $pdfFile->getClientMimeType();
                if ($mime_type == 'application/pdf' || $mime_type == 'application/octet-stream') {
                    $pdfFile->move($path, $pdf_file);
                    $filepath = $path . $pdf_file;
                    $manual->pdfFile = $filepath;
                } else {
                    return response()->json(['status' => false, 'messages' => "File must be pdf format"]);
                }
            }

            $manual->typeName = $request->get('typeName');
            $manual->details = $request->get('details');
            $manual->termsCondition = $request->get('termsCondition');
            $manual->status = $request->get('status');
            $manual->created_by = CommonFunction::getUserId();
            $manual->save();
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }
    /**************** Ending of User Manual related functions *********/

    /**************** Start of Home Page Articles related functions *********/

    public function homeArticlesList(Request $request)
    {

        $search_input = $request->get('search');
        $limit = $request->get('limit');
        $query = HomePageArticle::orderBy('page_name', 'desc');
        //        dd($query);
        if ($search_input) {
            $query->where(function ($query) use ($search_input) {
                $query->where('page_name', 'like', '%' . $search_input . '%');
            });
        }
        $data = $query->paginate($limit);
        $data->getCollection()->transform(function ($homepagearticles, $key) {
            return [
                'si' => $key + 1, 'id' => Encryption::encodeId($homepagearticles->id), 'page_name' => $homepagearticles->page_name,
                'page_content' => strip_tags($homepagearticles->page_content), 'page_content_en' => strip_tags($homepagearticles->page_content_en)
            ];
        });

        return response()->json($data);
    }

    public function homeArticlesStore(Request $request)
    {
        //         return $request->all();
        if (!ACL::getAccsessRight('settings', 'A')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $this->validate($request, [
            'page_content_en' => 'required',
            'page_content' => 'required',
            'page_name' => 'required'
        ]);
        try {
            HomePageArticle::create(
                array(
                    'page_content_en' => $request->get('page_content_en'),
                    'page_content' => $request->get('page_content'),
                    'page_name' => $request->get('page_name'),
                    'created_by' => CommonFunction::getUserId()
                )
            );

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }

    public function edithomeArticles($encrypted_id)
    {
        $id = Encryption::decodeId($encrypted_id);
        $data = HomePageArticle::where('id', $id)->first();
        //        $pdf = explode("/", $data->pdfFile);
        //        $filepdf = $pdf[2];
        return $data;
    }

    public function updatehomeArticles(Request $request)
    {

        if (!ACL::getAccsessRight('settings', 'E')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $id = Encryption::decodeId($request->id);

        $this->validate($request, [
            'page_content' => 'required',
            'page_content_en' => 'required',
            'page_name' => 'required'
        ]);
        try {
            $homearticles = HomePageArticle::Where('id', $id)->first();



            $homearticles->page_name = $request->get('page_name');
            $homearticles->page_content = $request->get('page_content');
            $homearticles->page_content_en = $request->get('page_content_en');
            $homearticles->created_by = CommonFunction::getUserId();
            $homearticles->save();
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }
    /**************** Ending of Home Page Articles related functions *********/

    /**************** Start of Whats new related functions *********/

    public function WhatsNewList(Request $request)
    {

        $search_input = $request->get('search');
        $limit = $request->get('limit');
        $query = WhatsNew::orderBy('id', 'desc');
        //        dd($query);
        if ($search_input) {
            $query->where(function ($query) use ($search_input) {
                $query->where('title', 'like', '%' . $search_input . '%')
                    ->orWhere('termsCondition', 'like', '%' . $search_input . '%');
            });
        }
        $data = $query->paginate($limit);
        $data->getCollection()->transform(function ($whatsnew, $key) {
            return [
                'si' => $key + 1, 'id' => Encryption::encodeId($whatsnew->id), 'title' => $whatsnew->title,
                'description' => strip_tags($whatsnew->description), 'image' => $whatsnew->image, 'is_active' => $whatsnew->is_active,
            ];
        });

        return response()->json($data);
    }

    public function whatsNewStore(Request $request)
    {
        if (!ACL::getAccsessRight('settings', 'A')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'is_active' => 'required'
        ]);
        try {

            $image = $request->file('image');
            $path = "uploads/whatsNew";
            if ($request->hasFile('image')) {
                $img_file = $image->getClientOriginalName();
                $mime_type = $image->getClientMimeType();
                if ($mime_type == 'image/jpeg' || $mime_type == 'image/jpg' || $mime_type == 'image/png' || $mime_type == 'image/webp') {
                    $image->move($path, $img_file);
                    $filepath = $path . '/' . $img_file;
                } else {
                    return response()->json(['status' => false, 'messages' => "Image must be png or jpg or jpeg format"]);
                }
            }
            $insert = WhatsNew::create(
                array(
                    'title' => $request->get('title'),
                    'description' => $request->get('description'),
                    'is_active' => $request->get('is_active'),
                    'image' => $filepath,
                    'created_by' => CommonFunction::getUserId()
                )
            );
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }


    public function editWhatsNew($encrypted_id)
    {
        $id = Encryption::decodeId($encrypted_id);
        $data = WhatsNew::where('id', $id)->first();
        $page_header = 'What\'s New';
        return $data;
    }

    public function updateWhatsNew(Request $request)
    {
        //        dd($id);

        if (!ACL::getAccsessRight('settings', 'E')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $id = Encryption::decodeId($request->id);
        //        dd($id);

        $this->validate($request, [
            'title' => 'required',
            'description' => 'required',
            'is_active' => 'required'
        ]);
        $whatsnew = WhatsNew::Where('id', $id)->first();
        $image = $request->file('image');
        $path = "uploads/whatsNew";

        if ($request->hasFile('image') && !empty($request->image)) {
            $img_file = $image->getClientOriginalName();
            $mime_type = $image->getClientMimeType();
            if ($mime_type == 'image/jpeg' || $mime_type == 'image/jpg' || $mime_type == 'image/png' || $mime_type == 'image/webp') {
                $image->move($path, $img_file);
                $filepath = $path . '/' . $img_file;
                $whatsnew->image = $filepath;
            } else {
                return response()->json(['status' => false, 'messages' => "Image must be png or jpg or jpeg format"]);
            }
        }
        try {

            $whatsnew->title = $request->get('title');
            $whatsnew->description = $request->get('description');
            $whatsnew->is_active = $request->get('is_active');
            $whatsnew->created_by = CommonFunction::getUserId();
            $whatsnew->save();
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }
    /**************** Ending of Whats new related functions *********/


    /**************** Start of Home page content related functions *********/
    public function homeContentList(Request $request)
    {

        $search_input = $request->get('search');
        $limit = $request->get('limit');
        $query = HomeContent::orderBy('id', 'desc');
        if ($search_input) {
            $query->where(function ($query) use ($search_input) {
                $query->where('title', 'like', '%' . $search_input . '%')
                    ->orWhere('heading', 'like', '%' . $search_input . '%')
                    ->orWhere('type', 'like', '%' . $search_input . '%');
            });
        }
        $data = $query->paginate($limit);
        $data->getCollection()->transform(function ($homecontent, $key) {
            return [
                'si' => $key + 1, 'id' => Encryption::encodeId($homecontent->id), 'title' => $homecontent->title, 'heading' => $homecontent->heading, 'type' => $homecontent->type, 'status' => $homecontent->status,
            ];
        });

        return response()->json($data);
    }
    public function homeContentStore(Request $request)
    {
        if (!ACL::getAccsessRight('settings', 'A')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $this->validate($request, [
            'title' => 'required',
            'type' => 'required',
            'title_en' => 'required',
            'heading_en' => 'required',
            'heading' => 'required',
            'details' => 'required',
            'details_en' => 'required',
            'details_url' => 'required',
            'order' => 'required',
            'status' => 'required'
        ]);
        try {
            $image = $request->file('image');
            $path = "uploads/homeContent";
            if ($request->hasFile('image')) {
                $img_file = $image->getClientOriginalName();
                $mime_type = $image->getClientMimeType();
                if ($mime_type == 'image/jpeg' || $mime_type == 'image/jpg' || $mime_type == 'image/png' || $mime_type == 'image/webp') {
                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }
                    $image->move($path, $img_file);
                    $filepath = $path . '/' . $img_file;
                } else {
                    return response()->json(['status' => false, 'messages' => "Image must be png or jpg or jpeg format"]);
                }
            }
            $icon = $request->file('icon');
            if ($request->hasFile('icon')) {
                $img_file = $icon->getClientOriginalName();
                $mime_type = $icon->getClientMimeType();
                if ($mime_type == 'image/jpeg' || $mime_type == 'image/jpg' || $mime_type == 'image/png' || $mime_type == 'image/webp') {
                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }
                    $icon->move($path, $img_file);
                    $iconfilepath = $path . '/' . $img_file;
                } else {
                    return response()->json(['status' => false, 'messages' => "Image must be png or jpg or jpeg format"]);
                }
            }

            $insert = HomeContent::create(
                array(
                    'type' => $request->get('type'),
                    'title' => $request->get('title'),
                    'title_en' => $request->get('title_en'),
                    'heading_en' => $request->get('heading_en'),
                    'heading' => $request->get('heading'),
                    'details_en' => $request->get('details_en'),
                    'details' => $request->get('details'),
                    'details_url' => $request->get('details_url'),
                    'order' => $request->get('order'),
                    'status' => $request->get('status'),
                    'image' => $filepath,
                    'icon' => $iconfilepath,
                    'created_by' => CommonFunction::getUserId()
                )
            );
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }
    public function edithomeContent($encrypted_id)
    {
        $id = Encryption::decodeId($encrypted_id);
        $data = HomeContent::where('id', $id)->first();
        $page_header = 'Home page Content';
        return $data;
    }
    public function updatehomeContent(Request $request)
    {

        if (!ACL::getAccsessRight('settings', 'E')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $id = Encryption::decodeId($request->id);
        //        dd($id);

        $this->validate($request, [
            'title' => 'required',
            'type' => 'required',
            'title_en' => 'required',
            'heading_en' => 'required',
            'heading' => 'required',
            'details' => 'required',
            'details_en' => 'required',
            'details_url' => 'required',
            'order' => 'required',
            'status' => 'required'
        ]);
        $homecontent = HomeContent::Where('id', $id)->first();
        $image = $request->file('image');
        $path = "uploads/homeContent";

        if ($request->hasFile('image') && !empty($request->image)) {
            $img_file = $image->getClientOriginalName();
            $mime_type = $image->getClientMimeType();
            if ($mime_type == 'image/jpeg' || $mime_type == 'image/jpg' || $mime_type == 'image/png' || $mime_type == 'image/webp') {
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $image->move($path, $img_file);
                $filepath = $path . '/' . $img_file;
                $homecontent->image = $filepath;
            } else {
                return response()->json(['status' => false, 'messages' => "Image must be png or jpg or jpeg format"]);
            }
        }
        $icon = $request->file('icon');
        $path = "uploads/homeContent";

        if ($request->hasFile('icon') && !empty($request->icon)) {
            $img_file = $icon->getClientOriginalName();
            $mime_type = $icon->getClientMimeType();
            if ($mime_type == 'image/jpeg' || $mime_type == 'image/jpg' || $mime_type == 'image/png' || $mime_type == 'image/webp') {
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }
                $icon->move($path, $img_file);
                $filepath = $path . '/' . $img_file;
                $homecontent->icon = $filepath;
            } else {
                return response()->json(['status' => false, 'messages' => "Image must be png or jpg or jpeg format"]);
            }
        }
        try {
            $homecontent->type = $request->get('type');
            $homecontent->title = $request->get('title');
            $homecontent->title_en = $request->get('title_en');
            $homecontent->heading_en = $request->get('heading_en');
            $homecontent->heading = $request->get('heading');
            $homecontent->details_en = $request->get('details_en');
            $homecontent->details = $request->get('details');
            $homecontent->details_url = $request->get('details_url');
            $homecontent->order = $request->get('order');
            $homecontent->status = $request->get('status');
            $homecontent->created_by = CommonFunction::getUserId();
            $homecontent->save();
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }
    /**************** Ending of Home page content related functions *********/

    /**************** Start of Logo related functions *********/

    public function storeLogo(request $request)
    {
        $logo = logo::Where('id', 1)->first();
        $company_logo = $request->file('company_logo');
        $path = "uploads/logo/";
        if ($request->hasFile('company_logo')) {
            $img_file = $company_logo->getClientOriginalName();
            $mime_type = $company_logo->getClientMimeType();
            if ($mime_type == 'image/jpeg' || $mime_type == 'image/jpg' || $mime_type == 'image/png' || $mime_type == 'image/webp') {
                $company_logo->move($path, $img_file);
                $filepath = $path . $img_file;
                $logo->logo = $filepath;
            } else {
                return response()->json(['status' => false, 'messages' => "Image must be png or jpg or jpeg format"]);
            }
        }
        try {
            $logo->title = $request->get('title');
            $logo->manage_by = $request->get('manage_by');
            $logo->help_link = $request->get('help_link');
            $logo->created_by = CommonFunction::getUserId();
            $logo->save();

            Artisan::call('cache:clear');
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }

    public function editLogo()
    {
        //        dd(123);
        $data = Logo::where('id', 1)->first();
        return $data;
    }
    /**************** Ending of Logo related functions *********/

    /**************** Start of PDF Print functions *********/

    public function pdfPrintRequest(Request $request)
    {

        $search_input = $request->get('search');
        $limit = $request->get('limit');
        $query = PdfPrintRequestQueue::leftJoin('process_list', 'pdf_print_requests_queue.app_id', '=', 'process_list.ref_id')
            ->whereRaw("`process_list`.`ref_id` = `pdf_print_requests_queue`.`app_id` and `process_list`.`process_type_id` = `pdf_print_requests_queue`.`process_type_id`")
            ->orderByRaw("`job_sending_status`= -9 DESC, `job_receiving_status`= -9 DESC, `id` DESC")
            ->select('pdf_print_requests_queue.*', 'process_list.tracking_no');
        //        dd($query);
        if ($search_input) {
            $query->where(function ($query) use ($search_input) {
                $query->where('tracking_no', 'like', '%' . $search_input . '%')
                    ->orWhere('prepared_json', 'like', '%' . $search_input . '%');
            });
        }
        $data = $query->paginate($limit);
        $data->getCollection()->transform(function ($pdfprint, $key) {
            return [
                'si' => $key + 1, 'id' => Encryption::encodeId($pdfprint->id),
                'tracking_no' => $pdfprint->tracking_no,
                'job_sending_response' => $pdfprint->job_sending_response,
                'job_receiving_response' => $pdfprint->job_receiving_response,
                'job_sending_status' => $pdfprint->job_sending_status,
                'no_of_try_job_sending' => $pdfprint->no_of_try_job_sending,
                'job_receiving_status' => $pdfprint->job_receiving_status,
                'no_of_try_job_receving' => $pdfprint->no_of_try_job_receving,
                'prepared_json' => $pdfprint->prepared_json,
                'certificate_link' => $pdfprint->certificate_link,
                //                'certificate_link' => Encryption::encode($pdfprint->certificate_name),
            ];
        });

        return response()->json($data);
    }

    public function editPdfPrintRequest($id)
    {
        $id = Encryption::decodeId($id);
        $data = PdfPrintRequestQueue::leftJoin('process_list', 'pdf_print_requests_queue.app_id', '=', 'process_list.ref_id')
            ->whereRaw("`process_list`.`ref_id` = `pdf_print_requests_queue`.`app_id` and `process_list`.`process_type_id` = `pdf_print_requests_queue`.`process_type_id`")
            ->where('pdf_print_requests_queue.id', $id)
            ->first([
                'pdf_print_requests_queue.*',
                'process_list.tracking_no',
            ]);
        return $data;
    }

    public function updatePdfPrintRequest(Request $request, $id)
    {
        //        dd(123);
        $id = Encryption::decodeId($id);

        if (!ACL::getAccsessRight('settings', 'E')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        try {
            $pdf_request = PdfPrintRequestQueue::find($id);
            $pdf_request->url_requests = $request->url_requests;
            $pdf_request->pdf_server_url = $request->pdf_server_url;
            $pdf_request->reg_key = $request->reg_key;
            $pdf_request->pdf_type = $request->pdf_type;
            $pdf_request->certificate_name = $request->certificate_name;
            $pdf_request->job_sending_response = $request->job_sending_response;
            $pdf_request->job_receiving_response = $request->job_receiving_response;
            $pdf_request->prepared_json = $request->prepared_json;
            $pdf_request->table_name = $request->table_name;
            $pdf_request->field_name = $request->field_name;
            $pdf_request->certificate_link = $request->certificate_link;
            $pdf_request->signatory = $request->signatory;
            $pdf_request->job_sending_status = $request->job_sending_status;
            $pdf_request->no_of_try_job_sending = $request->no_of_try_job_sending;
            $pdf_request->job_receiving_status = $request->job_receiving_status;
            $pdf_request->no_of_try_job_receving = $request->no_of_try_job_receving;
            $pdf_request->save();

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'messages'=>$e->getMessage()]);
        }
    }

    public function verifyPdfPrintRequest($pdf_id, $certificate_name)
    {
        $pdfId = Encryption::decodeId($pdf_id);
        $certificateName = Encryption::decode($certificate_name);

        $pdfRequests = DB::table('pdf_print_requests_queue')
            ->join('pdf_service_info', 'pdf_service_info.pdf_type', '=', 'pdf_print_requests_queue.pdf_type')
            ->where('pdf_service_info.certificate_name', $certificateName)
            ->where('pdf_print_requests_queue.id', $pdfId)
            ->get(['pdf_service_info.sql', 'pdf_print_requests_queue.app_id']);
        $app_id = $pdfRequests[0]->app_id;
        $sql = $pdfRequests[0]->sql;
        $requested_sql = str_replace("{app_id}", "$app_id", $sql);
        $result = DB::select(DB::raw("$requested_sql"));

        return json_encode($result);
    }

    public function resendPdfPrintRequest($id)
    {
        try {
            $id = Encryption::decodeId($id);
            $resend = PdfPrintRequestQueue::find($id);
            $resend->job_sending_status = 0;
            $resend->no_of_try_job_sending = 0;
            $resend->job_receiving_status = 0;
            $resend->no_of_try_job_receving = 0;
            $resend->certificate_link = '';
            $resend->save();

            return response()->json(['status' => true]);
        } catch (Exception $e) {
            return response()->json(['status' => false]);
        }
    }


    /**************** End of PDF Print functions *********/

    /**************** Start of Email sms queue functions *********/
    public function emailSmsQueueList(Request $request)
    {

        $search_input = $request->get('search');
        $limit = $request->get('limit');
        $query = EmailQueue::leftJoin('process_list', function ($join) {
            $join->on('email_queue.process_type_id', '=', 'process_list.process_type_id')
                ->on('email_queue.app_id', '=', 'process_list.ref_id');
        })
            ->orderBy('id', 'desc')
            ->select(
                'email_queue.id',
                'email_queue.caption',
                'email_queue.email_to',
                'email_queue.email_status',
                'email_queue.sms_to',
                'email_queue.sms_status',
                'process_list.tracking_no'
            );

        if ($search_input) {
            $query->where(function ($query) use ($search_input) {
                $query->where('caption', 'like', '%' . $search_input . '%')
                    ->orWhere('email_status', 'like', '%' . $search_input . '%')
                    ->orWhere('sms_status', 'like', '%' . $search_input . '%')
                    ->orWhere('tracking_no', 'like', '%' . $search_input . '%');
            });
        }
//        dd(474);
        $data = $query->paginate($limit);
        $data->getCollection()->transform(function ($emailsms, $key) {
            return [
                'si' => $key + 1, 'id' => Encryption::encodeId($emailsms->id),
                'tracking_no' => $emailsms->tracking_no,
                'email_to' => $emailsms->email_to,
                'email_status' => $emailsms->email_status,
                'sms_status' => $emailsms->sms_status,
                'sms_to' => $emailsms->sms_to,
                'caption' => $emailsms->caption,


            ];
        });

        return response()->json($data);
    }

    public function editEmailSmsQueue($id)
    {
        $decodedId = Encryption::decodeId($id);
        return EmailQueue::leftJoin('process_list', function ($join) {
            $join->on('email_queue.process_type_id', '=', 'process_list.process_type_id')
                ->on('email_queue.app_id', '=', 'process_list.ref_id');
        })
            ->where('email_queue.id', $decodedId)
            ->orderBy('id', 'desc')
            ->first([
                'process_list.tracking_no',
                'email_queue.*',
            ]);
    }

    public function updateEmailSmsQueue($id, Request $request)
    {
        if (!ACL::getAccsessRight('settings', 'E')) {
            die('You have no access right! Please contact system administration for more information.');
        }

        try {
            $decodedId = Encryption::decodeId($id);

            $this->validate($request, [
                'sms_to' => 'required',
                'sms_content' => 'required',
                'sms_status' => 'required',
                'email_to' => 'required',
                'email_cc' => 'required',
                'email_subject' => 'required',
                'email_content' => 'required',
                'email_status' => 'required'
            ]);

            $email_sms_record = EmailQueue::find($decodedId);
            $email_sms_record->sms_to = $request->get('sms_to');
            $email_sms_record->sms_content = $request->get('sms_content');
            $email_sms_record->sms_status = $request->get('sms_status');
            if ($request->get('sms_status') == 0) {
                $email_sms_record->sms_no_of_try = 0;
            }
            $email_sms_record->email_to = $request->get('email_to');
            $email_sms_record->email_cc = $request->get('email_cc');
            $email_sms_record->email_subject = $request->get('email_subject');
            $email_sms_record->email_content = $request->get('email_content');
            $email_sms_record->email_status = $request->get('email_status');
            if ($request->get('email_status') == 0) {
                $email_sms_record->email_no_of_try = 0;
            }
            $email_sms_record->save();

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'messages'=>$e->getMessage()]);
        }
    }

    public function resendEmailSmsQueue($id, $type)
    {
        $decodedId = Encryption::decodeId($id);
        $decodedType = $type;

        try {
            $emailSmsInfo = EmailQueue::find($decodedId);

            if (empty($emailSmsInfo)) {
                Session::flash('error', 'Information is not found![REQ-001]');
                return Redirect::back();
            }

            if ($decodedType == 'email') {
                $emailSmsInfo->email_status = 0;
                $emailSmsInfo->email_no_of_try = 0;
            } elseif ($decodedType == 'sms') {
                $emailSmsInfo->sms_status = 0;
                $emailSmsInfo->sms_no_of_try = 0;
            } elseif ($decodedType == 'both') {
                $emailSmsInfo->email_status = 0;
                $emailSmsInfo->sms_status = 0;
                $emailSmsInfo->email_no_of_try = 0;
                $emailSmsInfo->sms_no_of_try = 0;
            } else {
                Session::flash('error', 'Invalid format![REQ-001]');
                return Redirect::back();
            }

            $emailSmsInfo->save();
            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'messages'=>$e->getMessage()]);
        }
    }

    /**************** Start of Security functions *********/
    public function SecurityList(Request $request)
    {

        $search_input = $request->get('search');
        $limit = $request->get('limit');
        $query = SecurityProfile::orderBy('id');
        //        dd($query);
        if ($search_input) {
            $query->where(function ($query) use ($search_input) {
                $query->where('profile_name', 'like', '%' . $search_input . '%')
                    ->orWhere('week_off_days', 'like', '%' . $search_input . '%');
            });
        }
        $data = $query->paginate($limit);
        $data->getCollection()->transform(function ($security, $key) {
            return [
                'si' => $key + 1, 'id' => Encryption::encodeId($security->id),
                'profile_name' => $security->profile_name,
                'allowed_remote_ip' => $security->allowed_remote_ip,
                'week_off_days' => $security->week_off_days,
                'work_hour_start' => $security->work_hour_start,
                'work_hour_end' => $security->work_hour_end,
                'active_status' => $security->active_status,

            ];
        });

        return response()->json($data);
    }

    public function storeSecurity(Request $request)
    {

        $this->validate($request, [
            'profile_name' => 'required',
//            'allowed_remote_ip' => 'required',
            'week_off_days' => 'required',
            'work_hour_start' => 'required',
            'work_hour_end' => 'required',
            'alert_message' => 'required',
//            'active_status' => 'required'
        ]);

        $work_hour_start = date('H:i:s', strtotime($request->get('work_hour_start')));
        $work_hour_end = date('H:i:s', strtotime($request->get('work_hour_end')));

        try {
            $allowed_remote_ip = $request->get('allowed_remote_ip');
            if($allowed_remote_ip == 'null'  || $allowed_remote_ip == null || $allowed_remote_ip == ''){
                $allowed_remote_ip = '';
            }

            SecurityProfile::create(
                array(
                    'profile_name' => $request->get('profile_name'),
                    //                'sp_key' => $request->get('sp_key'),
                    ////                    'user_type' => $request->get('user_type'),
                    'user_email' => $request->get('user_email'),
                    'allowed_remote_ip' => $allowed_remote_ip,
                    'week_off_days' => implode(',', $request->get('week_off_days')),
                    'work_hour_start' => $work_hour_start,
                    'work_hour_end' => $work_hour_end,
                    'active_status' => $request->get('active_status'),
                    'alert_message' => $request->get('alert_message')
                )
            );

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            dd($e->getMessage());
            return response()->json(['status' => false]);
        }
    }

    public function editSecurity($id)
    {
        $id = Encryption::decodeId($id);
        $data = SecurityProfile::where('id', $id)->first();
        //        $user_types = UserTypes::pluck('type_name', 'id')->toArray();
        return $data;
    }

    public function updateSecurity($id, Request $request)
    {
//        dd($request->all());
        $_id = Encryption::decodeId($id);

        $this->validate($request, [
            'profile_name' => 'required',
//            'allowed_remote_ip' => 'required',
            'week_off_days' => 'required',
            'work_hour_start' => 'required',
            'work_hour_end' => 'required',
            'active_status' => 'required',
            'alert_message' => 'required'
        ]);
        try {
            $allowed_remote_ip = $request->get('allowed_remote_ip');
            if($allowed_remote_ip == 'null'  || $allowed_remote_ip == null || $allowed_remote_ip == ''){
                $allowed_remote_ip = '';
            }

            $work_hour_start = date('H:i:s', strtotime($request->get('work_hour_start')));
            $work_hour_end = date('H:i:s', strtotime($request->get('work_hour_end')));
            SecurityProfile::where('id', $_id)->update([
                'profile_name' => $request->get('profile_name'),
                //            'user_type' => $request->get('user_type'),
                'user_email' => $request->get('user_email'),
                'allowed_remote_ip' => $allowed_remote_ip,
                'week_off_days' => implode(',', $request->get('week_off_days')),
                'work_hour_start' => $work_hour_start,
                'work_hour_end' => $work_hour_end,
                'active_status' => $request->get('active_status'),
                'alert_message' => $request->get('alert_message')
            ]);

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            dd($request->all());
            return response()->json(['status' => false]);
        }
    }
    /**************** Ending of Security related Functions *********/

    /**************** Start of Service details related functions *********/

    public function ServiceDetailsList(Request $request)
    {

        $search_input = $request->get('search');
        $limit = $request->get('limit');
        $serviceList = ServiceDetails::leftJoin('process_type as pt', 'pt.id', '=', 'service_details.process_type_id')
            ->orderBy('service_details.ordering', 'asc')
            ->select('service_details.*', 'pt.name');
        if ($search_input) {
            $serviceList->where(function ($serviceList) use ($search_input) {
                $serviceList->where('name', 'like', '%' . $search_input . '%');
            });
        }
        $data = $serviceList->paginate($limit);
        $data->getCollection()->transform(function ($service, $key) {
            return [
                'si' => $key + 1, 'id' => Encryption::encodeId($service->id), 'name' => $service->name,
                'attachment' => $service->attachment, 'ordering' => $service->ordering, 'status' => $service->status,
            ];
        });

        return response()->json($data);
    }

    public function createServiceDetails()
    {
        $data = ProcessType::orderBy('name')->where('status', 1)->get();
        //        dd($data);
        return response()->json($data);
    }

    public function storeServiceDetails(Request $request)
    {
        if (!ACL::getAccsessRight('settings', 'A')) {
            die('You have no access right! Please contact system administration for more information.');
        }
        $this->validate($request, [
            'process_type_id' => 'required|unique:service_details',
            'ordering' => 'required',
            'status' => 'required'

        ]);
        try {
            $pdfFile = $request->file('attachment');
            //            dd($pdfFile);
            $filepath = '';
            $path = 'uploads/serviceForms/';
            if ($request->hasFile('attachment')) {
                $pdf_file = $pdfFile->getClientOriginalName();
                $mime_type = $pdfFile->getClientMimeType();
                if ($mime_type == 'application/pdf' || $mime_type == 'application/octet-stream') {
                    $pdfFile->move($path, $pdf_file);
                    $filepath = $path . $pdf_file;
                } else {
                    return response()->json(['status' => false, 'messages' => "File must be pdf format"]);
                }
            }
            $data = new ServiceDetails();
            $data->process_type_id = $request->get('process_type_id');
            $data->ordering = $request->get('ordering');
            $data->attachment = $filepath;
            $data->status = $request->get('status');
            $data->save();

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }

    public function editServiceDetails($id)
    {
        $decodedId = Encryption::decodeId($id);
        $data = ServiceDetails::where('id', $decodedId)->first();
        //        dd($data);
        return $data;
    }

    public function updateServiceDetails(Request $request)
    {
        $decodedId = $request->id;
        if (!ACL::getAccsessRight('settings', 'E')) {
            die('You have no access right! Please contact system administration for more information.');
        }

        $this->validate($request, [
            //            'process_type_id' => 'required|unique:service_details',
            'ordering' => 'required',
            'status' => 'required'
        ]);

        try {
            $roules = ServiceDetails::Where('id', $decodedId)->first();
            $pdfFile = $request->file('attachment');


            $path = 'uploads/serviceForms/';
            if ($request->hasFile('attachment')) {
                $pdf_file = $pdfFile->getClientOriginalName();
                $mime_type = $pdfFile->getClientMimeType();
                if ($mime_type == 'application/pdf' || $mime_type == 'application/octet-stream') {
                    $pdfFile->move($path, $pdf_file);
                    $filepath = $path . $pdf_file;
                    $roules->attachment = $filepath;
                } else {
                    return response()->json(['status' => false, 'messages' => "File must be pdf format"]);
                }
            }
            $roules->process_type_id = $request->get('process_type_id');
            $roules->ordering = $request->get('ordering');
            $roules->status = $request->get('status');
            $roules->save();

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }

    /**************** Ending of Service details related functions *********/


    public function applicationGuidelineCreate(Request $request)
    {
        $data['process_type'] = ProcessType::where('status', 1)
            ->orderBy('name_bn')
            ->pluck('group_name', 'group_name');
        //        $public_html = strval(view("Settings::application-guideline.create", $data))

        return view('Settings::application-guideline.create', $data);
    }

    public function applicationGuidelineStore(Request $request)
    {
        $this->validate($request, [
            'service_name' => 'required',
            'title_details' => 'required',
            'logo' => 'required',
            'service_heading' => 'required',
            'service_details' => 'required',
            'document_name' => 'required',
            'document_file' => 'required',

        ]);

        try {
            DB::beginTransaction();
            $app_guideline = new ApplicationGuideline();
            $app_guideline->group_nm_bn = $request->service_name;
            $app_guideline->details = $request->title_details;
            $service_logo = $request->file('logo');
            $path = "uploads/serviceLogo/";
            if ($request->hasFile('logo')) {
                $img_file = $service_logo->getClientOriginalName();
                $mime_type = $service_logo->getClientMimeType();
                if ($mime_type == 'image/jpeg' || $mime_type == 'image/jpg' || $mime_type == 'image/png' || $mime_type == 'image/webp') {
                    $service_logo->move($path, $img_file);
                    $filePath = $path . $img_file;
                    $app_guideline->logo = $filePath;
                } else {
                    \Session::flash('error', 'Logo must be png or jpg or jpeg format');
                    return redirect()->back();
                }
            }
            if ($request->tutorial_link != null) {
                $app_guideline->tutorial_link = $request->tutorial_link;
            }
            $app_guideline->status = 1;
            $app_guideline->save();

            if (!empty($request->service_heading)) {
                foreach ($request->service_heading as $item => $value) {
                    $app_guideline_details = new ApplicationGuidelineDetails();
                    $app_guideline_details->app_guideline_id = $app_guideline->id;
                    $app_guideline_details->service_heading = $value;
                    $app_guideline_details->details = $request->service_details[$item];
                    $app_guideline_details->status = 1;
                    $app_guideline_details->save();
                }
            }

            if (!empty($request->document_name)) {
                foreach ($request->document_name as $item => $value) {
                    $app_guideline_doc = new ApplicationGuidelineDoc();
                    $app_guideline_doc->app_guideline_id = $app_guideline->id;
                    $app_guideline_doc->doc_name = $value;

                    $pdfFile = $request->document_file[$item];
                    $path = "uploads/serviceDoc/";
                    if ($app_guideline_doc == !null) {
                        $pdf_file = $pdfFile->getClientOriginalName();
                        $mime_type = $pdfFile->getClientMimeType();
                        if ($mime_type == 'application/pdf' || $mime_type == 'application/octet-stream') {
                            $pdfFile->move($path, $pdf_file);
                            $filepath = $path . $pdf_file;
                        } else {
                            \Session::flash('error', 'File must be pdf format');
                            return redirect()->back();
                        }
                    }
                    $app_guideline_doc->file_path = $filepath;
                    $app_guideline_doc->status = 1;
                    $app_guideline_doc->save();
                }
            }

            DB::commit();
            Session::flash('success', 'Data entry successfully!');
            return Redirect('/settings/application-guideline');
        } catch (Exception $e) {
            return Redirect::back()->withInput();
        }
    }

    public function applicationGuideline()
    {
        return view('Settings::application-guideline.list');
    }

    public function applicationGuidelineList()
    {
        $data = ApplicationGuideline::orderBy('id', 'asc')->get();
        $mode = ACL::getAccsessRight('settings', 'E');

        return Datatables::of($data)
            ->addColumn('action', function ($data) use ($mode) {
                if ($mode) {
                    return '<a href="' . url('/settings/application-guideline/edit/' . Encryption::encodeId($data->id)) .
                        '" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i> পরিবর্তন করুন</a>';
                } else {
                    return '';
                }
            })
            ->editColumn('logo', function ($data) use ($mode) {
                $url = asset("$data->logo");
                return '<img src=' . $url . ' border="0" width="40" class="" align="center" />';
            })
            ->editColumn('details', function ($data) use ($mode) {
                $str = $data->details;
                //dd($str);
                $text  = implode(' ', array_slice(explode(' ', $str), 0, 8));
                return $text . '...';
            })->editColumn('status', function ($data) use ($mode) {
                $status = $data->status;
                if ($data->status == 1) {
                    return '<span class="text-success">Active</span>';
                } else {
                    return '<span class="text-danger">Inactive</span>';
                }
            })
            ->rawColumns(['logo', 'action', 'status'])
            ->make(true);
    }

    public function applicationGuidelineEdit($id)
    {
        $decodedId = Encryption::decodeId($id);
        $data['app'] = ApplicationGuideline::where('id', $decodedId)->first();
        $data['details'] = ApplicationGuidelineDetails::where('app_guideline_id', $decodedId)->get();
        $data['docs'] = ApplicationGuidelineDoc::where('app_guideline_id', $decodedId)->get();

        return view('Settings::application-guideline.edit', $data);
        //        return ProcessType::where('id', $decodedId)->first();
    }

    public function applicationGuidelineUpdate(Request $request)
    {
        $decodedId = Encryption::decodeId($request->app_id);
        $this->validate($request, [
            'title_details' => 'required',
            'status' => 'required',
            //            'document_name' => 'required',
            //            'document_file' => 'required',

        ]);
        try {
            DB::beginTransaction();
            $app_guideline = ApplicationGuideline::find($decodedId);

            $app_guideline->details = $request->title_details;
            $service_logo = $request->file('logo');
            $path = "uploads/serviceLogo/";
            if ($request->hasFile('logo')) {
                $img_file = $service_logo->getClientOriginalName();
                $mime_type = $service_logo->getClientMimeType();
                if ($mime_type == 'image/jpeg' || $mime_type == 'image/jpg' || $mime_type == 'image/png' || $mime_type == 'image/webp') {
                    $service_logo->move($path, $img_file);
                    $filePath = $path . $img_file;
                    $app_guideline->logo = $filePath;
                } else {
                    \Session::flash('error', 'Logo must be png or jpg or jpeg format');
                    return redirect()->back();
                }
            }
            if ($request->tutorial_link == !null) {
                $app_guideline->tutorial_link = $request->tutorial_link;
            }
            $app_guideline->status = $request->status;
            $app_guideline->save();

            if (!empty($request->service_heading)) {
                $detailsIds = [];
                foreach ($request->service_heading as $key => $value) {
                    //                    dd($request->service_details);
                    $detailsId = $request->get('details_id')[$key];
                    //                    dd($detailsId);
                    $appDetails = ApplicationGuidelineDetails::findOrNew($detailsId);
                    $appDetails->app_guideline_id = $app_guideline->id;
                    $appDetails->service_heading = $value;
                    $appDetails->details = $request->service_details[$key];
                    $appDetails->save();

                    $detailsIds[] = $appDetails->id;
                }
                if (!empty($detailsIds)) {
                    ApplicationGuidelineDetails::where('app_guideline_id', $app_guideline->id)->whereNotIn('id', $detailsIds)->delete();
                }
            }

            if (!empty($request->document_name)) {
                $docsIds = [];
                foreach ($request->document_name as $key => $value) {
                    //                    dd($request->service_details);
                    $docId = $request->get('document_id')[$key];
                    //                    dd($detailsId);
                    $appDocs = ApplicationGuidelineDoc::findOrNew($docId);
                    $appDocs->app_guideline_id = $app_guideline->id;
                    $appDocs->doc_name = $value;

                    //                    if (isset($request->document_file[$key])){
                    //
                    //                    }

                    if (isset($request->document_file[$key])) {
                        $pdfFile = $request->document_file[$key];
                        $path = "uploads/serviceDoc/";
                        if ($pdfFile != null) {
                            $pdf_file = $pdfFile->getClientOriginalName();
                            $mime_type = $pdfFile->getClientMimeType();
                            if ($mime_type == 'application/pdf' || $mime_type == 'application/octet-stream') {
                                $pdfFile->move($path, $pdf_file);
                                $filepath = $path . $pdf_file;
                            } else {
                                \Session::flash('error', 'File must be pdf format');
                                return redirect()->back();
                            }
                        }
                        $appDocs->file_path = $filepath;
                    }
                    $appDocs->save();

                    $docsIds[] = $appDocs->id;
                }
                if (!empty($docsIds)) {
                    ApplicationGuidelineDoc::where('app_guideline_id', $app_guideline->id)->whereNotIn('id', $docsIds)->delete();
                }
            }


            //            if (count($request->document_name) > 0){
            //                foreach ($request->document_name as $item => $value) {
            //                    $app_guideline_doc = new ApplicationGuidelineDoc();
            //                    $app_guideline_doc->app_guideline_id = $app_guideline->id;
            //                    $app_guideline_doc->doc_name = $value;
            //
            //                    $pdfFile = $request->document_file[$item];
            //                    $path = "uploads/serviceDoc/";
            //                    if ($app_guideline_doc == !null) {
            //                        $pdf_file = $pdfFile->getClientOriginalName();
            //                        $mime_type = $pdfFile->getClientMimeType();
            //                        if ($mime_type == 'application/pdf' || $mime_type == 'application/octet-stream') {
            //                            $pdfFile->move($path, $pdf_file);
            //                            $filepath = $path.$pdf_file;
            //                        } else {
            //                            \Session::flash('error', 'File must be pdf format');
            //                            return redirect()->back();
            //                        }
            //                    }
            //                    $app_guideline_doc->file_path = $filepath;
            //                    $app_guideline_doc->status = 1;
            //                    $app_guideline_doc->save();
            //                }
            //            }

            DB::commit();
            Session::flash('success', 'Data updated successfully!');
            return Redirect('/settings/application-guideline');
        } catch (Exception $e) {
            return Redirect::back()->withInput();
        }
    }


    public function getDistrict()
    {
        $disctrict = Area::where('area_type', 2)->orderBy('AREA_NM', 'ASC')->get();

        //        $data = ['responseCode' => 1, 'data' => ];
        return response()->json($disctrict);
    }

    /**************** Starting of Maintenance mode related Functions *********/

    public function maintenanceModeget()
    {
        $user_types = UserTypes::orderBy('id')->get();

        //        $maintenance_data = MaintenanceModeUser::findOrNew(1);
        //
        //        $allowed_user_array = (empty($maintenance_data->allowed_user_ids) ? [] : explode(',',
        //            $maintenance_data->allowed_user_ids));
        //
        //        $data = Users::leftjoin('user_types', 'user_types.id', '=', 'users.user_type')
        //            ->whereIn('users.id', $allowed_user_array)
        //            ->get([
        //                'users.id',
        //                'users.user_email',
        //                'users.user_first_name',
        //                'users.user_middle_name',
        //                'users.user_last_name',
        //                'user_types.type_name',
        //                'users.user_mobile'
        //            ]);

        return response()->json($user_types);
    }

    public function maintenanceModeStore(Request $request)
    {

        if ($request->has('submit_btn') && $request->get('submit_btn') == 'add_user') {
            $this->validate($request, [
                'user_email' => 'required|email'
            ]);
        } else {
            $rules = [];
            $rules['alert_message'] = 'required_if:operation_mode,==,2';
            $rules['operation_mode'] = 'required|numeric';

            $messages = [];
            $messages['alert_message.required_if'] = 'The alert message field is required when operation mode is Maintenance.';
            $this->validate($request, $rules, $messages);
        }


        try {

            if ($request->has('submit_btn') && $request->get('submit_btn') == 'add_user') {
                $user = Users::where('user_email', $request->get('user_email'))->first(['id']);
                if ($user) {
                    $maintenance_data = MaintenanceModeUser::find(1);
                    $allowed_user_array = (empty($maintenance_data->allowed_user_ids) ? [] : explode(
                        ',',
                        $maintenance_data->allowed_user_ids
                    ));

                    if (in_array($user->id, $allowed_user_array)) {
                        Session::flash('error', 'This user is already added [SC-320]');
                        return Redirect::back()->withInput();
                    }
                    array_push($allowed_user_array, $user->id);
                    $maintenance_data->allowed_user_ids = implode(',', $allowed_user_array);
                    $maintenance_data->save();
                    Session::flash('success', 'The user has been added successfully');
                    return Redirect::back()->withInput();
                }
                Session::flash('error', 'Invalid user email [SC-321]');
                return Redirect::back()->withInput();
            } else {
                $maintenance_data = MaintenanceModeUser::findOrNew(1);
                $maintenance_data->allowed_user_types = (empty($request->get('user_types')) ? '' : implode(
                    ',',
                    $request->get('user_types')
                ));
                $maintenance_data->alert_message = $request->get('alert_message');
                $maintenance_data->operation_mode = $request->get('operation_mode');
                $maintenance_data->save();

                Session::flash('success', 'Maintenance mode saved successfully!');
                return Redirect::back();
            }
        } catch (\Exception $e) {
            dd($e->getMessage(), $e->getLine(), $e->getFile());
            Session::flash('error', 'Sorry! Something Wrong.[SC-322]');
            return Redirect::back()->withInput();
        }
    }

    public function removeUserFromMaintenance($user_id)
    {
        $user_id = Encryption::decodeId($user_id);

        $maintenance_data = MaintenanceModeUser::find(1);

        $users_array = explode(',', $maintenance_data->allowed_user_ids);
        if (($key = array_search($user_id, $users_array)) !== false) {
            unset($users_array[$key]);
        }

        $maintenance_data->allowed_user_ids = (empty($user_id) ? '' : implode(',', $users_array));
        $maintenance_data->save();
        Session::flash('success', 'The user has been removed from allowed users.[SC-323]');
        return Redirect::back()->withInput();
    }
    /**************** Ending of Maintenance mode related Functions *********/
    /* End of Units related functions */
    function softDelete($model, $_id)
    {
        try {
            $id = Encryption::decodeId($_id);
            switch (true) {
                case ($model == "Area"):
                    $cond = Area::where('area_id', $id);
                    $list = 'area-list';
                    break;
                case ($model == "Bank"):
                    $cond = Bank::where('id', $id);
                    $list = 'bank-list';
                    break;
                case ($model == "park-info"):
                    $cond = ParkInfo::where('id', $id);
                    $list = 'park-info';
                    break;
                case ($model == "Branch"):
                    $cond = BankBranch::where('id', $id);
                    $list = 'branch-list';
                    break;
                case ($model == "Currency"):
                    $cond = Currencies::where('id', $id);
                    //                    dd($cond);
                    $list = 'currency';
                    break;
                case ($model == "Document"):
                    $cond = \App\Modules\Apps\Models\DocInfo::where('id', $id);
                    $list = 'document';
                    break;
                case ($model == "EcoZone"):
                    $cond = EconomicZones::where('id', $id);
                    $list = 'eco-zones';
                    break;
                case ($model == "HighCommissions"):
                    $cond = HighComissions::where('id', $id);
                    $list = 'high-commission';
                    break;
                case ($model == "hsCode"):
                    $cond = HsCodes::where('id', $id);
                    $list = 'hs-codes';
                    break;
                case ($model == "IndustryCategories"):
                    $cond = IndustryCategories::where('id', $id);
                    $list = 'indus-cat';
                    break;
                case ($model == "Notice"):
                    $cond = Notice::where('id', $id);
                    $list = 'notice';
                    break;
                case ($model == "Port"):
                    $cond = Ports::where('id', $id);
                    $list = 'ports';
                    break;
                case ($model == "Unit"):
                    $cond = Units::where('id', $id);
                    $list = 'units';
                    break;
                default:
                    Session::flash('error', 'Invalid Model! error code (Del-' . $model . ')');
                    return Redirect::back();
            }

            $cond->update([
                'is_archive' => 1,
                'updated_by' => CommonFunction::getUserId()
            ]);


            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }


}
