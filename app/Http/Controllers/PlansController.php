<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlansController extends Controller
{
    public function index(){
        $plans = Plan::active()->get();
        return inertia('Plans',['plans' => $plans]);
    }
}
