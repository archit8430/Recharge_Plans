<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class apiLog extends Model
{
    use HasFactory;

    protected $fillable= [
        'type',
        'http_method',
        'http_dode',
        'url',
        'headers',
        'body',
        'request_id',
        'ip_addr'
    ];
}