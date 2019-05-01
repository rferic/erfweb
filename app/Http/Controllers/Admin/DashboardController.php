<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct ()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index ()
    {
        $title = __('Dashboard');
        $description = __('See how the web is going. We hope everything goes perfectly!');
        $component = 'index-dashboard';

        return view('admin/default', compact('title', 'description', 'component'));
    }
}
