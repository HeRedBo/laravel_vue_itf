<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VenueBillDataType extends FormRequest
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
        $rules = [
            'venue_id'       => 'required|exists:venues,id',
            'type'           => 'required|in:1,2', // 1 入账 2 出账
            'name'           => 'required|unique:admin_venue_bill_data_type,name',
        ];
        if($this->get('id'))
        {
            $rules['name'] = 'required|unique:admin_venue_bill_data_type,name,'. $this->get('id');
        }
        return $rules;
    }
    
    public  function  attributes()
    {
        return [
            'venue_id'  => '道馆ID',
            'type'      => '账单类别',
            'name'      => '账单数据类型名称',
        ];
    }
}
