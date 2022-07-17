<?php

namespace App\Http\Requests\Admin\Advertising\Advertiser;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdvertiserStoreRequest extends FormRequest
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
            'all_categories' => ['nullable', Rule::in(['true', 'false'])],
            'all_regions' => ['nullable',Rule::in(['true', 'false'])],
            'name' => ['required'],
            'email' => ['nullable','email'],
            'phone' => ['nullable', 'numeric'],
            'website' => ['nullable', 'url'],
            'avatar' => ['nullable', 'mimes:png,jpg,jpeg']
        ];
    }

    public function messages()
    {
        return [
            'category_id.required' => 'هذه الحقل مطلوب',
            'category_id.numeric' => 'قيمة هذا الحقل لابد ان تكون ارقام',
            'region_id.required' => 'هذه الحقل مطلوب',
            'region_id.numeric' => 'قيمة هذا الحقل لابد ان تكون ارقام',
            'all_categories.required' => 'هذه الحقل مطلوب',
            'all_categories.required' => 'نوع البيانات غير صحيح',
            'all_regions.required' => 'هذه الحقل مطلوب',
            'all_regions.required' => 'نوع البيانات غير صحيح',
            'name.required' => 'هذه الحقل مطلوب',
            'email.email' => 'البريد الالكتروني غير صحيح',
            'phone.numeric' => 'قيمة هذا الحقل لابد ان تكون ارقام',
            'website.url' => 'الرابط غير صحيح',
            'avatar.mimes' => 'نوع الوسائط غير مدعوم',
        ];
    }
}
