<?php
namespace NaeemAwan\PredefinedLists\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;

class PredefinedList extends BaseModel
{
  use HasFactory, SoftDeletes;

  protected $table = 'predefined_list';

  protected $fillable = [
    'ltitle',
    'status',
    'parent_id',
    'descp',
    'image',
    'main_image',
    'price',
    'type',
  ];

  protected $casts = [
    'image' => 'object',
  ];

  public function scopeActive(Builder $query): Builder
  {
    return $query->where('status',1);
  }

  public function subOptionsCount()
  {
    return PredefinedList::where('parent_id',$this->id)->count('id');
  }

  public function detail()
  {
    return $this->hasOne(PredefinedListDetail::class,'list_id');
  }

  public function childitems()
  {
    return PredefinedList::where('parent_id',$this->id)->get();
  }

  public function frontcategories()
  {
    return PredefinedList::where('parent_id',$this->id)->whereIn('type',['color','trailer','motor'])->get();
  }
 
}
