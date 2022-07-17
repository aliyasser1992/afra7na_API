<?php

namespace App\Http\Requests\Admin\Advertising\Banner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BannerStoreRequest extends FormRequest
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
            'advertiser_id' => ['required', 'numeric'],
            'event_id' => ['required', 'numeric'],
            'price' => ['required', 'numeric'],
            'position' => ['required', 'numeric'],
            'type' => ['required', Rule::in(['video', 'text', 'image'])],
            'external_link' => ['nullable', 'url'],
            'content' => ['nullable', 'string'],
            'photo' => [
                'nullable',
                'mimes:png,jpg,jpeg'
            ]

        ];
    }

    public function messages()
    {
        return [
            'advertiser_id.required' => 'هذه الحقل مطلوب',
            'advertiser_id.numeric' => 'قيمة هذا الحقل لابد ان تكون ارقام',
            'event_id.required' => 'هذه الحقل مطلوب',
            'event_id.numeric' => 'قيمة هذا الحقل لابد ان تكون ارقام',
            'price.required' => 'هذه الحقل مطلوب',
            'price.numeric' => 'قيمة هذا الحقل لابد ان تكون ارقام',
            'position.required' => 'هذه الحقل مطلوب',
            'position.numeric' => 'قيمة هذا الحقل لابد ان تكون ارقام',
            'type.required' => 'هذه الحقل مطلوب',
            'type.in' => 'نوع الاعلان لابد ان يكون اي من فيديو , صورة او نص',
            'external_link.url' => 'الرابط غير صحيح',
            'photo.mimes' => 'نوع الوسائط غير مدعوم',
        ];
    }
}
