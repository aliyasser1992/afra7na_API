<?php

namespace App\Http\Requests\Admin\Advertising\Advertiser;

use Illuminate\Foundation\Http\FormRequest;

class AdvertiserUpdateRequest extends FormRequest
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
            'category_id' => ['required','numeric'],
            'region_id' => ['required', 'numeric'],
            'all_categories' => ['nullable','boolean'],
            'all_regions' => ['nullable','boolean'],
            'name' => ['required','string','max:159'],
            'email' => ['nullable','email'],
            'phone' => ['nullable', 'numeric'],
            'website' => ['nullable', 'url'],
            'avatar' => ['nullable', 'mimes:png,jpg,jpeg']
        ];
    }
}
