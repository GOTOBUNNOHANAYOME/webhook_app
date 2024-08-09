<?php

namespace App\Http\Controllers;

use App\Models\{
    LineAuthentication,
};
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LineController extends Controller

{
    public function redirectAccountLink(Request $request)
    {
        $link_token = $request->query('linkToken');
        $line_authentication = LineAuthentication::query()
            ->where('link_token', $link_token)
            ->first();

        if(is_null($line_authentication) || is_null($link_token)){
            abort(404);
        }

        $nonce = null;
        do{
            $nonce = Str::random(rand(10, 128));
        }while(LineAuthentication::where('nonce', $nonce)->exists());

        $line_authentication->nonce = $nonce;
        $line_authentication->save();

        return redirect()->away(config('line.link_nonce') . '?linkToken=' . $link_token . '&nonce=' . base64_encode($nonce));
    }
}
