<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
class profileEditRequest extends FormRequest{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        return [
            'user_first_name' => 'required', // First Name
            'user_last_name' => 'required', // Last Name
            'user_DOB' => 'required', //Date of Birth
            'user_phone' => 'required', //Mobile Number
        ];
    }

    public function messages() {
        return [
            'user_first_name.required' => 'First Name field is required',
            'user_last_name.required' => 'Last Name field is required',
            'user_DOB.required' => 'Date of Birth field is required',
            'user_phone.required' => 'Mobile Number field is required',
        ];
    }

}
