<?php

namespace App\Modules\Guides\Models;

use Illuminate\Database\Eloquent\Model;
use App\Libraries\CommonFunction;

class GuidesInfo extends Model {

    protected $table = 'guides_info';
    protected $fillable = [
      'id',
      'tracking_no',
      'identity',
      'full_name_bangla',
      'full_name_english',
      'father_name',
      'mother_name',
      'occupation',
      'birth_date',
      'national_id',
      'mobile',
      'pass_name',
      'pass_dob',
      'passport_no',
      'pass_issue_date',
      'pass_exp_date',
      'pass_issue_place',
      'pass_type',
      'pass_post_code',
      'pass_village',
      'pass_thana',
      'pass_district',
      'passport_master_id',
      'passport_last_status',
      'pass_issue_place_id',
      'per_village_ward',
      'per_police_station',
      'per_district',
      'per_post_code',
      'village_ward',
      'police_station',
      'district',
      'post_code',
      'gender',
      'marital_status',
      'dependent_id',
      'e_tin',
      'birth_certificate',
      'payment_status',
      'payment_by',
      'sb_status_id',
      'sb_status_by',
      'sb_district_id',
      'sb_thana_id',
      'sb_assigned_to',
      'sb_ref_no_1',
      'sb_ref_no_2',
      'sb_data_status',
      'created_at',
      'created_by',
      'reg_created_by',
      'reg_created_at',
      'reg_agency_id',
      'updated_at',
      'updated_by',
      'reg_updated_by',
      'is_locked',
      'is_archived',
      'user_type',
      'reg_user_type',
      'user_sub_type',
      'reg_user_sub_type',
      'is_govt',
      'deleted',
      'group_payment_id',
      'reg_payment_status',
      'reg_payment_id',
      'reg_voucher_id',
      'recog_voucher_id',
      'transfer_id',
      'transfer_stage_status',
      'nationality2',
      'resident',
      'draft_data_validity',
      'district_id',
      'thana_id',
      'per_district_id',
      'per_thana_id',
      'area_processed',
      'pilgrim_listing_id',
      'mother_name_english',
      'reg_payment_by',
      'husband_name',
      'spouse_name',
      'is_registrable',
      'father_name_english',
      'maharam_id',
      'maharam_relation',
      'is_revalidated_nid',
      'revalidate_at',
      'session_id',
      'hmis_pilgrim_id',
      'pre_reg_agency_id',
      'archived_by',
      'status_id',
      'flight_slot_id',
      'guide_form_link',
      'is_imported',
      'old_national_id',
      'pp_global_type',
      'ssc_institute_name',
      'ssc_passing_year',
      'ssc_board_name',
      'ssc_grade',
      'ssc_certificate_link',
      'hsc_institute_name',
      'hsc_passing_year',
      'hsc_board_name',
      'hsc_grade',
      'hsc_certificate_link',
      'honours_institute_name',
      'honours_passing_year',
      'honours_board_name',
      'honours_grade',
      'honours_certificate_link',
      'masters_institute_name',
      'masters_passing_year',
      'masters_board_name',
      'masters_grade',
      'masters_certificate_link',
      'experience',
      'additional_experience',
      'profile_pic',
      'present_address',
      'permanent_address',
      'designation',
      'office_name',
      'office_address',
      'present_division_name',
      'permanent_division_name',
      'present_division_id',
      'permanent_division_id',
      'prp_user_id',
      'birth_place',
      'birth_place_id',
      'is_employed',
      'is_experience',
      'previous_tracking_no',
      'pass_per_thana',
      'pass_per_village',
      'pass_per_post_code'
    ];

    public static function boot() {
        parent::boot();
        // Before update
        static::creating(function($post) {
            $post->created_by = CommonFunction::getUserId();
            $post->updated_by = CommonFunction::getUserId();
        });

        static::updating(function($post) {
            $post->updated_by = CommonFunction::getUserId();
        });
    }

    /*     * *****************************End of Model Function********************************* */
}