<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Blameable;
use App\Models\User;

class BlameableOnlyTables extends Model
{
  use Blameable;

  protected $dates = ['created_at','updated_at'];

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
