<?php

namespace NaeemAwan\PredefinedLists\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Botble\Base\Models\BaseModel;

class BoatDiscount extends BaseModel
{
  use HasFactory;

  protected $table = 'boat_discounts';

  protected $fillable = [
    'code',
    'list_id',
    'accessory_id',
    'discount',
    'discount_type',
    'valid_from',
    'valid_to',
    'never_expires'
  ];

  public function list()
  {
    return $this->belongsTo(PredefinedList::class, 'list_id');
  }
  public function accessory()
  {
    return $this->belongsTo(PredefinedList::class, 'accessory_id');
  }
}
