<?php

namespace App\Modules\Bulletin\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulletinRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'bulletinType' => 'required',
            'publicationDateAndTime' => 'required',
            'bulletinDate' => 'required',
            'bulletinDateEng' => 'required',
            'nonGovHajjPassengerCount' => 'required',
            'totalFlightCount' => 'required',
            'soudiaFlightCount' => 'required',
            'itHelpDesk' => 'required',
            'numberOfPublication' => 'required',
            'totalHajjPassenger' => 'required',
            'totalGovHajjPassenger' => 'required',
            'bimanFlightNumberCount' => 'required',
            'flynasFlightNumberCount' => 'required',
            'medicalPaperCount' => 'required',
            'additional_txt' => 'nullable',
            'activities' => 'required',
            'bulletinId' => 'nullable',
            'bulletinNewsId' => 'nullable',
            'bimanPassengerCount' => 'required',
            'soudiaPassengerCount' => 'required',
            'flynasPassengerCount' => 'required',
        ];
    }
}
