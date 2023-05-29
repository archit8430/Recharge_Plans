<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tbl_commission extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'commission_percentage'        
    ];

    public function company()
    {
        return $this->belongsTo(tbl_company::class,'company_id','id');
    }
}