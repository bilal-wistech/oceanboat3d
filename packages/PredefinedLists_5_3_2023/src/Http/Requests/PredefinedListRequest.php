<?php

namespace NaeemAwan\PredefinedLists\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class PredefinedListRequest extends Request
{
    public function rules(): array
    {
        return [
            'ltitle'=>'required',
            'category_id'=>'required_if:parent_id,0',
            'descp'=>'nullable|string',
            'status'=>'required|integer',
            'image' => 'required|array|min:3|max:4',
            'image.0' => 'nullable',
            'image.1' => 'required',
            'image.2' => 'required',
            'image.3' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'image' => 'The image field must contain at 3 images.',
            'image.min' => 'The image field must contain 3 images.',
            'image.max' => 'The image field must contain 3 images.',
            'image.1.required' => 'The first image is required.',
            'image.2.required' => 'The second image is required.',
            'image.3.required' => 'The third image is required.',
        ];
    }
}
