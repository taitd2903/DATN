<?php

namespace App\Http\Controllers\Admin\Statistics;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    public function index()
    {
       
        return view('Admin.statistics.index');
    }

}
