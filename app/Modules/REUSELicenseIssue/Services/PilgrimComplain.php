<?php

namespace App\Modules\REUSELicenseIssue\Services;

use App\Libraries\Encryption;
use App\Libraries\PostApiData;
use App\Modules\ProcessPath\Models\GuideRequestPilgrim;
use App\Modules\ProcessPath\Models\PilgrimDataList;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\ProcessPath\Models\ProcessType;
use App\Modules\REUSELicenseIssue\Http\FormHandler;
use App\Modules\REUSELicenseIssue\Models\HajjSessions;
use App\Modules\Web\Models\Complain;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class PilgrimComplain extends FormHandler
{
    public function __construct(Object $processInfo)
    {

        parent::__construct($processInfo);
    }

    public function createForm(): string
    {
        return true;
    }

    public function storeForm(Request $request): RedirectResponse
    {

        return true;
        // TODO: Implement storeForm() method.
    }

    public function viewForm($processTypeId, $applicationId): JsonResponse
    {
        $appmasterId = Encryption::decodeId($applicationId);
        $pilgrimdata = PilgrimDataList::with('processlist')->where('id', $appmasterId)
            ->first();
        $data = array();
        $data['data'] = Complain::where('pilgrim_data_list_id',$appmasterId)->first();
        $data['session_id'] = $this->session_id;
        $data['process_type_id'] = $processTypeId;
        $public_html = (string)view("REUSELicenseIssue::PilgrimComplain.view", $data);
        return response()->json(['responseCode' => 1, 'html' => $public_html]);

    }

    public function editForm($processTypeId, $applicationId): JsonResponse
    {
        return true;
    }
}
