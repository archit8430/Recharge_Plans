<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_status_log extends Model
{
    use HasFactory;

    protected $fillable = [
        'recharge_id',
        'old_status',
        'new_status'        
    ];

}