<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VenueScheduleExtendRequest extends FormRequest
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
             'schedule_id'   => 'required',
             'schedule_date' => 'required||date',
             'start_time'    => 'required',
             'end_time'      => 'required',
             'class_id'      => '',
             'week'          => 'required',
             'section'       => 'required',
        ];
    }

    
    public  function  attributes()
    {
        return [
             'schedule_id'   => '道馆课程表ID',
             'schedule_date' => '时间日期',
             'start_time'    => '课程有效期开始时间',
             'end_time'      => '课程效期结束时间',
             'class_id'      => '班级ID',
             'week'          => '星期几',
             'section'       => '节次',
        ];
    }
}
