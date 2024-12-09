<?php

namespace App\Modules\REUSELicenseIssue\Http;

use App\Modules\REUSELicenseIssue\Models\HajjSessions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

abstract class FormHandler implements ProcessConstantsId
{
    protected $process_type_name;
    protected $process_type_id;
    protected $acl_name;
    protected $api_url;

    public function __construct($processInfo)
    {
        $this->session_id = HajjSessions::where('state', 'active')->first(['caption', 'id as session_id']);
        $this->process_type_id = $processInfo->process_type_id ?? null;
        $this->process_type_name = $processInfo->name ?? '';
        $this->acl_name = $processInfo->acl_name ?? '';
        $this->api_url =  env('API_URL');
    }

    public abstract function createForm(): string;

    public abstract function storeForm(Request $request): RedirectResponse;

    public abstract function viewForm(Request $request, $applicationId): JsonResponse;

    public abstract function editForm(Request $request, $applicationId): JsonResponse;
}
