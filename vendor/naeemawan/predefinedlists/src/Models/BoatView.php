<?php

namespace NaeemAwan\PredefinedLists\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Botble\Contact\Enums\ContactStatusEnum;

class BoatView extends BaseModel
{
	use HasFactory;

	protected $table = 'boat_views';

    protected $fillable = [
        'entity_id',
        'entity_type',
        'total_count',

    ];

    public function option()
	{
	  return $this->BelongsTo(PredefinedList::class,'entity_id');
	}

}
