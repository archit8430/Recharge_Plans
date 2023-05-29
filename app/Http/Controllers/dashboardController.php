<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\board;
use App\Models\task_board_mapping;
use App\Models\tbl_recharge;


class dashboardController extends Controller
{

    public function dashboard(){        
        return view('dashboard');
    }

    public function companyReport(){     
        return view('companyReport');
    }

    public function salesReport(){     
        return view('salesReport');
    }

    public function commReport(){
        return view('commReport');
    }
}