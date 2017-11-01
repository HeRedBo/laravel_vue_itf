<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentUpdateRequest extends FormRequest
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
            'name'           => 'required|min:2|max:255',
            'sex'            => 'required',
            'birthday'       => 'required|date',
            'sign_up_at'     => 'required|date',
            'venue_id'       => 'required',
            'class_id'       => 'required',
            'user_contacts'   => 'required',
        ];
    }
    
    public  function  attributes()
    {
        return [
            'name'           => '学生姓名',
            'sex'            => '性别',
            'birthday'       => '学生生日',
            'sign_up_at'     => '报名时间',
            'venue_id'       => '道馆ID',
            'user_contacts'   => '联系人信息',
            'class_id'       => '归属班级',
        ];
    }
}
