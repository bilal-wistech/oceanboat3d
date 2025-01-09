<?php

namespace NaeemAwan\PredefinedLists\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Botble\Contact\Enums\ContactStatusEnum;

class BoatEnquiry extends BaseModel
{
	use HasFactory;

	protected $table = 'boat_enquiries';

	protected $fillable = [
		'id',
		'name',
		'email',
		'phone_number',
		'message',
		'boat_id',
		'total_price',
		'status',
	];

	protected $casts = [
        'status' => ContactStatusEnum::class,
    ];

	public function boat()
	{
	  return $this->BelongsTo(PredefinedList::class,'boat_id');
	}

	public function details()
	{
		return $this->hasMany(BoatEnquiryDetail::class,'enquiry_id');
	}
}
