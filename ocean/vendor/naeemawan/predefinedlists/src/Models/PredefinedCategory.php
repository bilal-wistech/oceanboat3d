<?php

namespace NaeemAwan\PredefinedLists\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class PredefinedCategory extends BaseModel
{
	use HasFactory;

	protected $table = 'predefined_categories';

	protected $fillable = [
		'id',
		'name',
		'description',
		'status',
	];
}
