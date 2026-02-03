<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        return view('site.dashboard', [
            'user' => $user,
            'siteId' => $user->site_id,
        ]);
    }
}
