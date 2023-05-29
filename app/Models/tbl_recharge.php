<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_recharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'mobile',
        'amoumt',
        'status',
        'recharge_data'        
    ];
}