<?php

namespace Botble\Theme\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class ParallelSliderRequest extends Request
{
    public function rules(): array
    {
        return [
            'title'=>'required',
            'image'=>'required',
            'status'=>'required|integer',
        ];
    }
}
