<?php
namespace Botble\Theme\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class ParallelSlider extends BaseModel
{
  use HasFactory, SoftDeletes;

  protected $table = 'parallel_slider';

  protected $fillable = [
    'title',
    'status',
    'description',
    'image',
    'action',
    'action_title',
  ];
 
}
