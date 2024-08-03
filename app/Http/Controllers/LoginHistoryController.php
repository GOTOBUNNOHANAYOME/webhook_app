<?php

namespace App\Http\Controllers;

use App\Models\{
    LoginHistory,
    User,
};
use App\Http\Requests\LoginHistoryRequest;
use Illuminate\Http\Request;

class LoginHistoryController extends Controller
{
    public function create(Request $request)
    {
        return view('login_histories.create');
    }

    public function store(LoginHistoryRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        auth()->login($user);

        LoginHistory::create([
            'user_id'    => auth()->id(),
            'ip'         => get_ip(),
            'user_agent' => $request->header('User-Agent')
        ]);
    }
}
