<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentSignRequest extends FormRequest
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
        $before_date = date("Y-m-d",strtotime("+1 day"));
        return [
            'venue_id'     => 'required',
            'class_id'     => 'required',
            'student_ids'  => 'required',
            'section'      => 'required',
            'sign_date'    => 'required|date|before:'.$before_date,
            'status'       => 'required',
        ];
    }

    public  function  attributes()
    {
        return [
            'venue_id'    => '道馆ID',
            'class_id'    => '班级ID',
            'student_ids' => '签到学生',
            'section'     => '节次',
            'sign_date'   => '签到日期',
            'status'      => '签到状态',
        ];
    }
}
