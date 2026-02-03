<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CorporateController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        return view('corporate.dashboard', [
            'user' => $user,
        ]);
    }
}
