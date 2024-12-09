<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Libraries\Encryption;

class UserUpdateRequest extends FormRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        //dd($_POST);
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $id = null;
        $segment = $this->segment(3) ? $this->segment(3) : '';
        if ($segment) {
            $id = Encryption::decodeId($segment);
        }

        $rules = [];
        $rules['user_first_name'] = 'required';
        $rules['user_last_name'] = 'required';
        $rules['user_gender'] = 'required';
        $rules['user_type'] = 'required';
        $rules['nationality'] = 'required';
        $rules['passport_no'] = 'required_if:identity_type,1';
        $rules['user_nid'] = 'required_if:identity_type,2';
        $rules['user_DOB'] = 'required|date';
        $rules['country'] = 'required';

        $rules['division'] = 'required_if:country,BD';
        $rules['district'] = 'required_if:country,BD';
        $rules['thana'] = 'required_if:country,BD';
        $rules['state'] = 'required_unless:country,BD';
        $rules['province'] = 'required_unless:country,BD';

        $rules['user_mobile'] = 'required';


        return $rules;
    }

    public function messages() {
        return [
            'user_first_name.required' => 'First Name field is required',
            'user_last_name.required' => 'Last Name field is required',
            'user_DOB.required' => 'Date of Birth field is required',
            'user_phone.required' => 'Mobile Number field is required',
            'user_email.required' => 'Email Address field is required',
            'user_email.unique' => 'Email Address must be unique'
        ];
    }

}
