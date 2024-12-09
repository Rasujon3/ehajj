<?php

namespace App\Libraries;

use App\Modules\ProcessPath\Models\ProcessList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ACL
{

    public static function db_reconnect()
    {
        if (Session::get('DB_MODE') == 'PRODUCTION') {
        }
    }

    public static function hasOwnCompanyUserModificationRight($userType, $right, $id)
    {
        try {
            $companyId = CommonFunction::getUserCompanyByUserId($id);
            if ($companyId == Auth::user()->working_company_id)
                return true;

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }


    public static function getAccsessRight($module, $right = '', $id = null,$active_menu_for_arr=[])
    {
        $accessRight = '';
        if (Auth::user()) {
            $user_type = Auth::user()->user_type;

        } else {
            die('You are not authorized user or your session has been expired!');
        }
        switch ($module) {
            case 'settings':
                if ($user_type == '1x101') {
                    $accessRight = 'AVE';
                } elseif ($user_type == '2x202') {
                    $accessRight = 'A-V-E-';
                } elseif ($user_type == '4x404') {
                    $accessRight = 'A-V-E-';
                }
                break;
            case 'dashboard':
                if ($user_type == '1x101') {
                    $accessRight = 'AVESERN';
                } elseif ($user_type == '5x505') {
                    $accessRight = 'AVESERNH';
                } elseif ($user_type == '13x131') {
                    $accessRight = 'AVESERNH';
                }
                break;

            case 'reportv2':
                if ($user_type == '1x101' || $user_type == '15x151') {
                    $accessRight = 'AVE';
                } else if ($user_type == '5x505' || $user_type == '6x606') {
                    $accessRight = 'V';
                } else {
                    $accessRight = 'V';
                }
                break;

            case 'CompanyAssociation':
                if ($user_type == '1x101') {
                    $accessRight = '-A-V-UP-';
                } else if (in_array($user_type, ['5x505', '6x606'])) {
                    $accessRight = '-A-V-E-';
                } else {
                    $accessRight = '-V-';
                }
                break;

            case 'Documents':
                if (in_array($user_type, ['5x505', '6x606'])) {
                    $accessRight = '-A-V-E-';
                } else {
                    $accessRight = '-V-';
                }
                break;

            case 'user':
                if (in_array($user_type, ['1x101'])) {
                    $accessRight = '-A-V-E-R-APV-';
                } elseif (in_array($user_type, ['5x505', '6x606']) && in_array($right, ['-APV-'])) {
                    if (ACL::hasOwnCompanyUserModificationRight($user_type, $right, $id)) {
                        return true;
                    }
                    $accessRight = '-V-R-';
                } else if (in_array($user_type, ['2x202', '4x404'])) {
                    $accessRight = '-V-R-';
                } else {
                    $accessRight = '-V-';
                }
                if ($right == "SPU") {
                    if (ACL::hasUserModificationRight($user_type, $right, $id))
                        return true;
                }
                break;

            case 'IndustryNew':
                if (in_array($user_type, ['1x101', '2x202'])) { //7x707=MIS, 8x808=IT Officer
                    $accessRight = '-V-';
                } else if (in_array($user_type, ['5x505'])) {
                    $accessRight = '-A-V-E-';
                } else if (in_array($user_type, ['4x404'])) {
                    $accessRight = '-V-UP-';
                }
                break;

            case 'IndustryReRegistration':
                if (in_array($user_type, ['1x101', '2x202'])) { //7x707=MIS, 8x808=IT Officer
                    $accessRight = '-V-';
                } else if (in_array($user_type, ['5x505'])) {
                    $accessRight = '-A-V-E-';
                } else if (in_array($user_type, ['4x404'])) {
                    $accessRight = '-V-UP-';
                }
                break;

            case 'Payment':
                if (in_array($user_type, ['1x101', '2x202', '7x707', '8x808'])) { //7x707=MIS, 8x808=IT Officer
                    $accessRight = '-V-';
                } else if (in_array($user_type, ['5x505'])) {
                    $accessRight = '-A-V-E-';
                    if ($id != null && !(strpos($accessRight, $right) === false)) {
                        if (ACL::hasApplicationModificationRight(10, $id, $right) == false)
                            return false;
                    }
                } else if (in_array($user_type, ['4x404'])) {
                    $accessRight = '-V-UP-';
                }
                break;

            case 'pilgrim_listing_govt':
                if (in_array($user_type, $active_menu_for_arr)) {
                    $accessRight = '-A-V-E-UP-';
                } else{
                    if (in_array($user_type, ['1x101','2x202','2x203','3x301','3x305','3x308','4x401','15x115'])) {
                        $accessRight = '-V-';
                    } else if (in_array($user_type, ['5x505'])) {
                        $accessRight = '-V-';
                    } else if (in_array($user_type, ['4x404'])) {
                        $accessRight = '-A-V-E-UP-';
                    }
                }
                break;

            case 'pilgrim_listing_private':
                if (in_array($user_type, $active_menu_for_arr)) {
                    $accessRight = '-A-V-E-UP-';
                } else{
                    if (in_array($user_type, ['1x101','2x202','2x203','3x301','3x305','3x308','4x401','15x115'])) {
                        $accessRight = '-V-';
                    } else if (in_array($user_type, ['5x505'])) {
                        $accessRight = '-V-';
                    } else if (in_array($user_type, ['4x404'])) {
                        $accessRight = '-A-V-E-UP-';
                    }
                }
                break;

            case 'travel_plan':
                if (in_array($user_type, ['1x101','2x202','2x203','3x301','3x305','3x308','4x401','15x115'])) {
                    $accessRight = '-V-';
                } else if (in_array($user_type, ['5x505'])) {
                    $accessRight = '-V-';
                } else if (in_array($user_type, ['4x404'])) {
                    $accessRight = '-A-V-E-UP-';
                }else if (in_array($user_type, ['21x101'])) {
                    $accessRight = '-A-V-E-UP-';
                }
                break;
            case 'material_receive':
                if (in_array($user_type, ['1x101', '2x202', '4x404', '17x171'])) {
                    $accessRight = '-V-';
                } else if (in_array($user_type, ['17x173'])) {
                    $accessRight = '-A-V-E-UP-';
                }
                break;
            case 'material_issue':
                if (in_array($user_type, ['1x101', '2x202', '4x404', '17x171'])) {
                    $accessRight = '-V-';
                } else if (in_array($user_type, ['17x173'])) {
                    $accessRight = '-A-V-E-UP-';
                }
                break;
            case 'gender_change_request':
                if (in_array($user_type, $active_menu_for_arr)) {
                    $accessRight = '-A-V-E-UP-';
                } else{
                    if (in_array($user_type, ['1x101','2x202','2x203','3x301','3x305','3x308','4x401','15x115'])) {
                        $accessRight = '-V-';
                    } else if (in_array($user_type, ['5x505'])) {
                        $accessRight = '-V-';
                    } else if (in_array($user_type, ['4x404'])) {
                        $accessRight = '-A-V-E-UP-';
                    }
                }
                break;

            case 'hajj_canceled':
                if (in_array($user_type, ['1x101', '2x202'])) {
                    $accessRight = '-V-A-E-';
                } else if (in_array($user_type, ['1x101','2x202','2x203','3x301','3x305','3x308','4x401','15x115'])) {
                    $accessRight = '-V-';
                } else if (in_array($user_type, ['4x404','2x202','3x301','3x302'])) {
                    $accessRight = '-A-V-E-UP-';
                }
                break;


            case 'pilgrim_assign_by_guide':

                if (in_array($user_type, ['1x101','2x202','2x203','3x301','3x305','3x308','4x401','15x115'])) {
                    $accessRight = '-V-';
                }else if (in_array($user_type, ['4x404','21x101'])) {
                    $accessRight = '-A-V-E-UP-';
                }
                break;


            case 'sticker_visa':
                if (in_array($user_type, ['10x410','10x411','10x412','10x413','10x414','11x420','11x421','11x422','12x430','12x431','12x432','13x131','15x151','16x161','16x162','17x171','17x172','17x173','18_415','1x101','1x110','20x665','20x666','21x101','2x202','2x203','2x205','2x206','3x300','3x301','3x302','3x304','3x305','3x306','3x308','4x401','4x402','4x404','6x606','6x607','7x710','7x711','7x712','7x713'])) {
                    $accessRight = '-V-A-E-';
                } else if (in_array($user_type, ['5x505'])) {
                    $accessRight = '-V-';
                } else if (in_array($user_type, ['4x404','2x202','3x301','3x302'])) {
                    $accessRight = '-A-V-E-UP-';
                }
                break;
            case 'pilgrim_complain':
                 if (in_array($user_type, ['4x404'])) {
                    $accessRight = '-A-V-E-UP-';
                }
                break;
            case 'news':
                $user_type_arr = (count($active_menu_for_arr) > 0) ? $active_menu_for_arr : ['2x202'];
                if (in_array($user_type, $user_type_arr)) {
                    $accessRight = '-A-V-E-UP-';
                }
                break;
            case 'flight':
                if (in_array($user_type, ['6x606','6x607','4x401'])) {
                    $accessRight = '-A-V-E-UP-';
                }
                break;
            case 'pilgrim':
                if (in_array($user_type, ['18x415'])) {
                    $accessRight = '-A-V-E-UP-';
                }else if (in_array($user_type, ['18x415'])) {
                    $accessRight = 'V';
                }
                break;
            case 'registration':
                if (in_array($user_type, ['18x415'])) {
                    $accessRight = '-A-V-E-UP-';
                }else if (in_array($user_type, ['18x415'])) {
                    $accessRight = 'V';
                }
                break;
            case 'guides':
                if (in_array($user_type, ['18x415']) && Auth::user()->working_user_type == 'Guide') {
                    $accessRight = '-A-V-E-UP-';
                }else if (in_array($user_type, ['18x415']) && Auth::user()->working_user_type == 'Guide') {
                    $accessRight = 'V';
                }
                break;
            case 'agency_info_update':
                if (in_array($user_type, $active_menu_for_arr)) {
                    $accessRight = '-A-V-E-UP-';
                } else{
                    if (in_array($user_type, ['1x101','2x202','2x203','3x301','3x305','3x308','4x401','15x115'])) {
                        $accessRight = '-V-';
                    }
                }
                break;

            default:
                $accessRight = '';
        }
        if ($right != '') {
            if (strpos($accessRight, $right) === false) {
                return false;
            } else {
                return true;
            }
        } else {
            return $accessRight;
        }
    }
    public static function hasUserModificationRight($userType, $right, $id)
    {
        try {
            $userId = CommonFunction::getUserId();
            if ($userType == '1x101')
                return true;

            if ($userId == $id)
                return true;

            return false;
        } catch (\Exception $e) {
            dd(CommonFunction::showErrorPublic($e->getMessage()));
            return false;
        }
    }

    public static function isAllowed($accessMode, $right)
    {
        if (strpos($accessMode, $right) === false) {
            return false;
        } else {
            return true;
        }
    }

    public static function hasApplicationModificationRight($processTypeId, $id, $right)
    {
        try {
            $companyId = CommonFunction::getUserCompanyWithZero();
            if ($right != '-E-') {
                return true;
            } else {
                $processListData = ProcessList::where('ref_id', $id)->where('process_type_id', $processTypeId)
                    ->first(['company_id', 'status_id']);
                if ($processListData == null) {
                    return false;
                } elseif ($processListData->company_id == $companyId && in_array($processListData->status_id, [-1, 5])) {
                    return true;
                } else {
                    return false;
                }
            }
        } catch (\Exception $e) {
            dd(CommonFunction::showErrorPublic($e->getMessage()));
            return false;
        }
    }

    /*     * **********************************End of Class****************************************** */
}
