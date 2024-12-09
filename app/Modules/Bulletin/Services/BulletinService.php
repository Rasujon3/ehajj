<?php

namespace App\Modules\Bulletin\Services;

use App\Http\Controllers\CommonController;
use App\Libraries\CommonFunction;
use App\Libraries\Encryption;
use App\Modules\Bulletin\Exceptions\BulletinException;
use App\Modules\Bulletin\Models\BulletinMaster;
use App\Modules\Bulletin\Models\BulletinTemplate;
use App\Modules\News\Models\News;
use Illuminate\Support\Facades\Auth;

class BulletinService
{
    private $authId;

    public function __construct()
    {
        $this->authId = Auth::id();
    }

    /**
     * @throws BulletinException
     */
    public function preview($bulletinData, $editMode = false): array
    {
        #Fetch the bulletin template data
        $template = BulletinTemplate::where('haj_type', $bulletinData->bulletinType)->first();

        # Check if template data is empty or bulletin text is empty
        if (empty($template) || empty($template->bulletin_subject) || empty($template->bulletin_text)) {
            throw new BulletinException("টেমপ্লেটের ডাটা পাওয়া যায় নাই।", 404);
        }

        # Prepare data for bulletin creation
        $bulletinSubject = str_replace('{$bulletin_date_bn}', $bulletinData->bulletinDate, $template->bulletin_subject);
        $data = [
            'bulletin_number' => $bulletinData->numberOfPublication,
            'bulletin_subject' => $bulletinSubject,
            'haj_type' => $bulletinData->bulletinType,
            'bulletin_date' => $bulletinData->bulletinDateEng,
            'bulletin_date_bn' => $bulletinData->bulletinDate,
            'publish_date_time' => $bulletinData->publicationDateAndTime,
            'total_pilgrim' => $bulletinData->totalHajjPassenger,
            'govt_pilgrim' => $bulletinData->totalGovHajjPassenger,
            'pvt_pilgrim' => $bulletinData->nonGovHajjPassengerCount,
            'total_flight' => $bulletinData->totalFlightCount,
            'bg_flight' => $bulletinData->bimanFlightNumberCount,
            'sv_flight' => $bulletinData->soudiaFlightCount,
            'xy_flight' => $bulletinData->flynasFlightNumberCount,
            'it_helpdesk_service' => $bulletinData->itHelpDesk,
            'medical_service' => $bulletinData->medicalPaperCount,
            'main_massage_text' => $bulletinData->additional_txt,
            'fixed_text' => $bulletinData->activities,
            'status' => 1,
            'updated_by' => $this->authId,
            'bg_pilgrims' => $bulletinData->bimanPassengerCount,
            'soudia_pilgrims' => $bulletinData->soudiaPassengerCount,
            'flynas_pilgrims' => $bulletinData->flynasPassengerCount,
        ];

        if ($editMode) {
            $data['bulletinId'] = $bulletinData->bulletinId;
            $data['bulletinNewsId'] = $bulletinData->bulletinNewsId;
        } else {
            $data['created_by'] = $this->authId;
        }

        # Bulletin Content Ready
        $bulletinContent = $template->bulletin_text;
        $mailContent = $template->mail_text;
        $bulletinTemplateInstance = (new BulletinTemplateService($bulletinContent, $mailContent,$data));
        $contents = $bulletinTemplateInstance->prepareContent();
        $data['email_template'] = $contents['emailContent'];
        $serializedBulletinData = serialize($data);
        $serializedBulletinContent = serialize($contents['content']);
        return compact('serializedBulletinContent', 'bulletinSubject', 'serializedBulletinData');
    }

    /**
     * @throws \Exception
     */
    public function publish($bulletinData, $bulletinTemplate, $editMode = false, $flag = "save")
    {
        # Store Bulletin Data and retrieve
        $bulletinMasterObject = $editMode ? BulletinMaster::findOrFail(Encryption::decodeId($bulletinData->bulletinId)) : new BulletinMaster();
        $bulletinMasterObject->fill((array)$bulletinData);
        $bulletinMasterObject->save();
        # Bulletin Content Ready
        $bulletinMasterObject->update(['mail_text_html' => $bulletinData->email_template, 'mail_status' => 0]);

        if ($flag == "save") {
            # Bulletin Content Publish
            $newsObject = ($editMode && $bulletinData->bulletinNewsId !== null) ? News::findOrFail(Encryption::decodeId($bulletinData->bulletinNewsId)) : new News();
            $newsData = [
                'bulletin_master_id' => $bulletinMasterObject->id,
                'title' => $bulletinData->bulletin_subject,
                'description' => $bulletinTemplate,
                'file_path' => '',
                'publish_date' => date('Y-m-d H:i:s', time()),
                'post_status' => 'publish',
                'post_type' => 'hajj_bulletin'
            ];
            $newsObject->fill($newsData);
            $newsObject->save();
        }
    }


}
