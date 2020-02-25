<?php

namespace App\Race\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class StoreRaceRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string|max:50|min:1'
        ];
    }
}
