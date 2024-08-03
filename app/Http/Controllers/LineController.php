<?php

namespace App\Http\Controllers;

use App\Models\LineAccount;
use App\Enums\LineRequestType;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class LineController extends Controller

{
    public function handle(Request $request)
    {
        $request_data = json_decode($request->getContent());
        $type = $request_data->events->type;

        switch($type){
            case LineRequestType::FOLLOW:
                $this->followEvent($request_data);
                break;
        }
    }

    public function followEvent($request_data)
    {
        $line_user_id = $request_data->events->sourse->userId;

        $line_account = LineAccount::where('line_user_id', $line_user_id)->first();

        if(is_null($line_account)){
            $client = new Client();

            $headers = [
                'Authorization' => 'Bearer ' . config('line.access_token')
            ];

            $response = $client->request('GET', config('line.get_profile') . '/' . $line_user_id, [
                'headers' => $headers,
            ]);

            if($response->getStatusCode() !== 200){
                abort(404);
            }

            $response_body = json_decode($response->getBody()->getContents());

            LineAccount::create([
                'name'          => $response_body->displayName,
                'line_user_id'  => $line_user_id,
                'language'      => $response_body->language,
                'icon_path'     => $response_body->pictureUrl,
                'is_enable'     => true
            ]);
        }

        if(!$line_account->is_enable){
            $line_account->is_enable = true;
            $line_account->save();
        }
    }
}
