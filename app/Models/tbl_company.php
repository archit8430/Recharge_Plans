<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_company extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name'
    ];

    public function commission()
    {
        return $this->hasOne(tbl_commission::class,'company_id','id');
    }
}