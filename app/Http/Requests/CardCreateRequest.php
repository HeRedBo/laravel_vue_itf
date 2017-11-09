<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CardCreateRequest extends FormRequest
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
            'type' => 'required',
            'name' => 'required|unique:cards|min:2|max:255',
            'venue_id' => 'required|numeric',
            'card_price' => 'required|numeric',
            'unit' => '',
            'number' => 'required|numeric',
        ];
        $type = $this->get('type');
        if($type == 1)
        {
            $rules['unit'] = 'required';
        }
        return $rules;
    }
    
    
    public  function  attributes() 
    {
        $attributes = [
            'name'     => '卡券名称',
            'type'     => '卡券类型',
            'venue_id' => '道馆ID',
            'unit'     => '计数单位',
            'card_price'=> '卡券价格',
            'number'   => '数量',
        ];
        $type = $this->get('type');
        if($type ==2)
            $attributes['number'] = '次数';
        return $attributes;
    }
}
