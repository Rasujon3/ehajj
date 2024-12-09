<?php

namespace App\Console\Commands;

use App\Libraries\CommonFunction;
use App\Modules\Documents\Models\ApplicationDocuments;
use App\Modules\ProcessPath\Models\ProcessDoc;
use App\Modules\ProcessPath\Models\ProcessList;
use App\Modules\ProcessPath\Models\ProcessType;
use App\Modules\Settings\Models\AppDocuments;
use App\Modules\Users\Models\Users;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use ZipArchive;

class ShadowFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shadow:demo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {

            // Get pending request for shadow file from Database
            $shadowFileInfo = \App\Modules\Settings\Models\ShadowFile::where('is_generate', 0)->get([
                'id',
                'shadow_file_perimeter'
            ]);

            // if there have no request in queue, then exit.
            if ($shadowFileInfo->isEmpty()) {
                echo "No request in queue to generate shadow file! \n";
                exit;
            }


            foreach ($shadowFileInfo as $data) {
                $shadowFileId = $data->id;
                $jsonData = json_decode($data->shadow_file_perimeter);
                $process_id = $jsonData->process_id;
                $module_name = str_replace("", '', $jsonData->module_name);
                $process_type_id = $jsonData->process_type_id;
                $app_id = $jsonData->app_id;

                // Set application model namespace as a dynamic format
                $application_model_namespace = "/App/Modules/$module_name/Models/" . $module_name;
                $application_model_namespace = str_replace('/', '\\', $application_model_namespace);

                $application_data = $application_model_namespace::where('id', $app_id)->first();
                $process_data = ProcessList::leftJoin('process_type', 'process_type.id', '=', 'process_list.process_type_id')
                    ->where('process_list.id', $process_id)
                    ->first([
                        'process_type.name',
                        'process_list.*'
                    ]);
                $tracking_no = $process_data->tracking_no;

                // Get all process documents of application
                $processDocuments = ProcessDoc::where('process_type_id', $process_type_id)
                    ->where('ref_id', $process_id)
                    ->get(['file']);

                // Get all application attachment
                $application_attachments = ApplicationDocuments::where('ref_id', $app_id)
                    ->where('process_type_id', $process_type_id)
                    ->get(['uploaded_path']);

                $process_history = DB::select(DB::raw("select  `process_list_hist`.`desk_id`,`as`.`status_name`,
                                `process_list_hist`.`process_id`,
                                if(`process_list_hist`.`desk_id`=0,\"-\",`ud`.`desk_name`) `deskname`,
                                concat(user_first_name, user_middle_name, user_last_name) as user_full_name,
                                `process_list_hist`.`updated_by`,
                                `process_list_hist`.`status_id`,
                                `process_list_hist`.`process_desc`,
                                `process_list_hist`.`process_id`,
                                `process_list_hist`.`updated_at`,
                                 group_concat(`pd`.`file`) as files
                                from `process_list_hist`
                                left join `process_documents` as `pd` on `process_list_hist`.`id` = `pd`.`process_hist_id`
                                left join `user_desk` as `ud` on `process_list_hist`.`desk_id` = `ud`.`id`
                                left join `users` on `process_list_hist`.`updated_by` = `users`.`id`

                                left join `process_status` as `as` on `process_list_hist`.`status_id` = `as`.`id`
                                and `process_list_hist`.`process_type` = `as`.`process_type_id`
                                where `process_list_hist`.`process_id`  = '$process_id'
                                and `process_list_hist`.`process_type` = '$process_type_id'

                                and `process_list_hist`.`status_id` != -1
                    group by `process_list_hist`.`process_id`,`process_list_hist`.`desk_id`, `process_list_hist`.`status_id`, process_list_hist.updated_at
                    order by process_list_hist.updated_at desc

                    "));

                $server_path = config('app.SERVER_PUBLIC_DIR_PATH');
                if (substr($server_path, -1) != '/') {
                    $server_path .= '/';
                }

                // Application information text file creation
                $app_info_text_file_path = $server_path . 'shadow-file/AppInfo_' . $tracking_no . '.txt';
                $process_path_info = "Process Path:" . json_encode($process_history);
                $application_info = "Application Data:" . json_encode($application_data);
                $process_list_info = "Process List Data:" . json_encode($process_data);
                $app_info_text_file_content = $application_info . "\n \n" . $process_list_info . "\n \n" . $process_path_info;
                File::put($app_info_text_file_path, $app_info_text_file_content);

                // Read.me file creation
                $read_me_file_path = $server_path . 'readme_' . $tracking_no . '.md';
                $read_me_file_content = "## shadow file generated by " . $this->getApplicantName($application_data->created_by) . "  for the application of $process_data->name. \nTracking Number: $tracking_no \nGenerate Date: " . date('d/m/Y h:i:s');
                File::put($read_me_file_path, $read_me_file_content);


                // Declare an array for archive files
                $appsFiles = [];

                // add application attachment in the archive file array
                if ($application_attachments->isNotEmpty()) {
                    foreach ($application_attachments as $key => $attachment) {
                        if (!empty($attachment->doc_file_path)) {
                            $appsFiles[] = $server_path . "uploads/" . $attachment->uploaded_path;
                        }
                    }
                }

                // add process document in the archive file array
                if ($processDocuments->isNotEmpty()) {
                    foreach ($processDocuments as $doc) {
                        if (!empty($doc->file)) {
                            $appsFiles[] = $server_path . $doc->file;
                        }
                    }
                }

                // add Read.me and Application.Info file in the archive file array
                array_push($appsFiles, $app_info_text_file_path, $read_me_file_path);

                $shadow_file_dir = public_path("shadow-file/");
                if (!file_exists($shadow_file_dir)) {
                    mkdir($shadow_file_dir, 0777, true);
                }

                $filePath = public_path("shadow-file/" . date("Y") . "/" . date("m") . "/");
                if (!file_exists($filePath)) {
                    mkdir($filePath, 0777, true);
                }

                $file_unique_id = uniqid();
                $fileDir = $filePath . $module_name . '_' . $tracking_no . '_' . $file_unique_id . '.zip';
                $file_path_for_db = 'shadow-file/' . date("Y") . '/' . date("m") . '/' . $module_name . '_' . $tracking_no . '_' . $file_unique_id . '.zip';
                $archive = new ZipArchive();

                if ($archive->open($fileDir, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {

                    foreach ($appsFiles as $file) {

                        if ($archive->addFile($file, basename($file))) {
                            continue;
                        } else {

                            // if error, then delete AppInfo and readme file
                            File::delete($app_info_text_file_path);
                            File::delete($read_me_file_path);

                            if (!file_exists($file)) {
                                \App\Modules\Settings\Models\ShadowFile::where('id', $shadowFileId)->update([
                                    'process_type_id' => $process_type_id,
                                    'ref_id' => $app_id,
                                    'is_generate' => -1,
                                    'error_messages' => "file path : $file not found"
                                ]);
                                throw new Exception("file `{$file}` path not found");
                            }

                            \App\Modules\Settings\Models\ShadowFile::where('id', $shadowFileId)->update([
                                'process_type_id' => $process_type_id,
                                'ref_id' => $app_id,
                                'is_generate' => -1,
                                'error_messages' => "file $file could not be added to the zip file: " . $archive->getStatusString()
                            ]);
                            throw new Exception("file `{$file}` could not be added to the zip file: " . $archive->getStatusString());
                        }
                    }

                    if ($archive->close()) {
                        \App\Modules\Settings\Models\ShadowFile::where('id', $shadowFileId)->update([
                            'file_path' => $file_path_for_db,
                            'process_type_id' => $process_type_id,
                            'ref_id' => $app_id,
                            'is_generate' => 1,
                        ]);

                        File::delete($app_info_text_file_path);
                        File::delete($read_me_file_path);
                        echo "Shadow file generated successfully! \n";
                    }

                } else {
                    throw new Exception("could not close zip file: " . $archive->getStatusString());
                }
            }

        } catch (Exception $e) {
            echo 'Something went wrong !!!';
            echo "\nMessage : " . $e->getMessage() . "\n";
            echo "\nLine : " . $e->getLine() . "\n";
            echo "\nFile : " . $e->getFile() . "\n";
            exit;
        }
    }

    public function getApplicantName($user_id)
    {
        $applicant_name = Users::where('id', $user_id)->first(['user_first_name', 'user_middle_name', 'user_last_name']);
        if (empty($applicant_name)) {
            $applicant_name = "Test User";
        } else {
            $applicant_name = $applicant_name->user_first_name . ' ' . $applicant_name->user_middle_name . ' ' . $applicant_name->user_last_name;
        }

        return $applicant_name;
    }
}
