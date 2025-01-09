<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class BoatView extends Model
{
    protected $table = 'boat_views';

    protected $fillable = [
        'entity_id',
        'entity_type',
        'total_count',
        
    ];

}
