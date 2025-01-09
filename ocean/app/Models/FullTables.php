<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Blameable;
use App\Traits\Sortable;
use App\Models\User;

class FullTables extends Model
{
  use Blameable;
  use SoftDeletes;

  protected $dates = ['created_at','updated_at','deleted_at'];

  /**
  * Get created by.
  */
  public function createdBy()
  {
    return $this->belongsTo(User::class,'created_by');
  }

  /**
  * Get updated by.
  */
  public function updatedBy()
  {
    return $this->belongsTo(User::class,'updated_by');
  }
}
