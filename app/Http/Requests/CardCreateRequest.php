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
        return [
            'name' => 'required|unique:cards|min:2|max:255',
            'venue_id' => 'required|numeric',
            'unit' => 'required',
            'card_price' => 'required|numeric',
            'number' => 'required|numeric',
        ];
    }
    
    
    public  function  attributes() 
    {
        return [
            'name'     => '卡券名称',
            'venue_id' => '道馆ID',
            'unit'     => '计数单位',
            'card_price'=> '卡券价格',
            'number'   => '数量',
        ];
    }
}
