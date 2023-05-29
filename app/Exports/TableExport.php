<?php

namespace App\Exports;

use App\Models\Recharge;
use Maatwebsite\Excel\Concerns\FromCollection;

class TableExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Recharge::all();
    }
}
