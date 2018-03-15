<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentCardCreateRequest extends FormRequest
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
            'student_id'   => 'required|numeric|exists:admin_students,id',
            'user_cards'   => 'required|array',
        ];
    }


    public  function  attributes()
    {
        return [
            'student_id'     => '学生ID',
            'user_cards'     => '学生卡券信息',
        ];
    }
}
