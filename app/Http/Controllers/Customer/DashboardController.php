<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
class DashboardController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:customer');
    }

    public function index()
    {
        return view('customer.dashboard');
    }
}
