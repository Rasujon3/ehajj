<?php namespace App\Modules\Web\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Settings\Models\PdfPrintRequestQueue;
use DB;

class VerifyDocController extends Controller
{

    public function verifyDoc($docId, $pdfType='')
    {
//        $pdfCertificate = PdfPrintRequestQueue::where('doc_id', $docId)->first();

        $pdfCertificate = collect(DB::select(DB::raw("SELECT pdf_print_requests_queue.certificate_link AS certificate_link,
       process_type.name_bn as process_name,process_type.group_name, submitted_at.process_id, submitted_at.company_id,
            applicant.user_first_name AS applicant_name, applicant.user_email AS applicant_email,
            submitted_at.hash_value AS applicant_hash, submitted_at.updated_at as applicant_time, submitted_at.json_object, applicant.user_mobile as applicant_phone,
            approver.user_first_name AS approver_name, approver.user_email AS approver_email,
            approved_at.hash_value AS approver_hash, approved_at.updated_at as approval_time, approver.user_mobile as approver_phone
            FROM pdf_print_requests_queue
            LEFT JOIN process_type on process_type.id = pdf_print_requests_queue.process_type_id
            LEFT JOIN process_list_hist as submitted_at on submitted_at.ref_id = pdf_print_requests_queue.app_id and submitted_at.process_type_id = pdf_print_requests_queue.process_type_id and submitted_at.status_id = 1
            LEFT JOIN process_list_hist as approved_at on approved_at.ref_id = pdf_print_requests_queue.app_id and approved_at.process_type_id = pdf_print_requests_queue.process_type_id and approved_at.status_id = 25
            LEFT JOIN users as applicant ON applicant.id = submitted_at.updated_by
            LEFT JOIN users as approver ON approver.id = pdf_print_requests_queue.signatory
            WHERE pdf_print_requests_queue.doc_id = '$docId'
            order by submitted_at.id desc limit 1")))->first();

        return view('Web::view-certificate', compact('pdfCertificate'));
    }

//    public function verifyDoc1()
//    {
//        return view('Web::view-certificate');
//    }
}


