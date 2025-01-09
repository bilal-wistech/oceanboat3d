<?php

namespace NaeemAwan\PredefinedLists\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Botble\Contact\Enums\ContactStatusEnum;

class BoatEnquiryDetail extends BaseModel
{
	use HasFactory;

	protected $table = 'boat_enquiry_details';

	protected $fillable = [
		'id',
		'enquiry_id',
		'subcat_id',
		'option_id',
	];

    public function enquiry_option()
	{
	  return $this->BelongsTo(PredefinedList::class,'option_id');
	}

	public function slug()
	{
	  return $this->BelongsTo(PredefinedList::class,'subcat_slug','type');
	}
	
}
