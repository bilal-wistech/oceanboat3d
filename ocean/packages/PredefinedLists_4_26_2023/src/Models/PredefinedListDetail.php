<?php
namespace NaeemAwan\PredefinedLists\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Botble\Base\Models\BaseModel;

class PredefinedListDetail extends BaseModel
{
  use HasFactory;

  protected $table = 'predefined_list_detail';

  public $timestamps = false;

  protected $fillable = [
    'list_id',
    'category_id',
    'url',
    'images',
    'details',
  ];
  
  protected $casts = [
    'images' => 'object',
  ];

  /**
  * Get list.
  */
  public function mainRow()
  {
    return $this->belongsTo(PredefinedList::class,'list_id');
  }

  public function category()
  {
    return $this->belongsTo(PredefinedCategory::class,'category_id');
  }

}
