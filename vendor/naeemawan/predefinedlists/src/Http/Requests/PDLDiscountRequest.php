<?php

namespace NaeemAwan\PredefinedLists\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class PDLDiscountRequest extends Request
{
    public function rules(): array
    {
        return [
            // 'name'=>'required',
            // 'status'=>'required|integer',
        ];
    }
}
