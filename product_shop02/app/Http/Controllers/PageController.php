<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\View\View;

/**
 * Public marketing pages: the SaaS landing page and the privacy policy.
 */
class PageController extends Controller
{
    public function landing(): View
    {
        return view('landing', [
            'plans' => Plan::active()->ordered()->get(),
        ]);
    }

    public function pricing(): View
    {
        return view('pricing', [
            'plans' => Plan::active()->ordered()->get(),
        ]);
    }

    public function privacy(): View
    {
        return view('privacy');
    }
}
