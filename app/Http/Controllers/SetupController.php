<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SetupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        if ($user->setup_completed) {
            return redirect('/dashboard');
        }
        return view('setup.wizard', compact('user'));
    }

    public function complete(Request $req)
    {
        $user = auth()->user();
        $data = $req->validate([
            'business_name'    => 'required|string|max:191',
            'business_city'    => 'nullable|string|max:100',
            'business_address' => 'nullable|string|max:255',
            'currency'         => 'required|string|max:10',
            'fiscal_year_start'=> 'nullable|string|max:20',
        ]);
        $data['setup_completed'] = true;
        $user->update($data);

        return redirect('/subscription/plans')->with('setup_done', true);
    }
}
