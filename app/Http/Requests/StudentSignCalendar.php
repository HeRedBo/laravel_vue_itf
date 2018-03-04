<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentSignCalendar extends FormRequest
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
            'venue_id' => 'required|numeric',
            'class_id' => 'numeric',
            'date'     => 'date',
        ];
    }

    public  function  attributes()
    {
        return [
            'venue_id'   => '道馆ID',
            'class_id'   => '班级ID',
            'date'       => '查询日期',
        ];
    }
}
