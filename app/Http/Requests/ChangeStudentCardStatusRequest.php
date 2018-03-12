<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeStudentCardStatusRequest extends FormRequest
{
    const STUDENT_CART_CLOSE_STATUS = 3;
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
        $rule = [
            'id'   => 'required|numeric',
            'status' => 'required|in:0,1,2',
            'remark'   => '',
        ];
        $status = $this->get('status');
        if($status == self::STUDENT_CART_CLOSE_STATUS)
            $rule['remark'] = 'required';
        return $rule;
    }

    public  function  attributes()
    {
        return [
            'id'       => '学生卡券记录ID',
            'status'   => '修改状态',
            'remark'   => '修改备注',
        ];
    }
}
