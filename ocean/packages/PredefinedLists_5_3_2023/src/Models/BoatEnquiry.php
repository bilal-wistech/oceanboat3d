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
		'color',
		'motor',
		'trailer',
		'canvas-covers',
		'fishing-locator',
		'general',
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

	public function color_option()
	{
	  return $this->BelongsTo(PredefinedList::class,'color');
	}

	public function motor_option()
	{
	  return $this->BelongsTo(PredefinedList::class,'motor');
	}

	public function trailor_option()
	{
	  return $this->BelongsTo(PredefinedList::class,'trailor');
	}

	public function canvas_option()
	{
	  return $this->BelongsTo(PredefinedList::class,'canvas_covers');
	}

	public function fishing_option()
	{
	  return $this->BelongsTo(PredefinedList::class,'fishing_locator');
	}

	public function general_option()
	{
	  return $this->BelongsTo(PredefinedList::class,'general');
	}
}
