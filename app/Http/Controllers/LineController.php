<?php

namespace App\Http\Controllers;

use App\Models\LineAccount;
use App\Enums\LineRequestType;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class LineController extends Controller

{
    public function handle(Request $request)
    {
        try{
            $request_data = json_decode($request->getContent());
            $type = $request_data->events[0]->type;

            switch($type){
                case LineRequestType::FOLLOW:
                    $this->followEvent($request_data);
                    break;
                case LineRequestType::UNFOLLOW:
                    $this->unfollowEvent($request_data);
                    break;
                default;
            }
        }catch(\Exception $e){
            Log::channel('line')->error('error', [
                'message'  => $e->getMessage(),
                $request_data
            ]);
        }

        return response('OK', 200);
    }

    public function followEvent($request_data)
    {
        $line_user_id = $request_data->events[0]->source->userId;

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

            $response_body = json_decode($response->getBody()->getContents(), false);

            $line_account = LineAccount::create([
                'name'          => $response_body->displayName,
                'line_user_id'  => $line_user_id,
                'language'      => $response_body->language,
                'icon_path'     => $response_body->pictureUrl,
                'is_enable'     => true
            ]);
        }

        $user = $line_account->user;
        if(is_null($user)){
            $client = new Client();

            $headers = [
                'Authorization' => 'Bearer ' . config('line.access_token')
            ];

            $response = $client->request('POST', config('line.account_link') . '/' . $line_user_id . '/linkToken', [
                'headers' => $headers,
            ]);

            if($response->getStatusCode() !== 200){
                abort(404);
            }

            $response_body = json_decode($response->getBody()->getContents(), false);
            
            $request_body = [
                'to'       => $line_account->line_user_id,
                'messages' => [
                    [
                        'type'     => 'template',
                        'altText'  => 'Account Link',
                        'template' => [
                            'type'    => 'buttons',
                            'text'    => 'Account Link',
                            'actions' => [
                                [
                                    'type'  => 'uri',
                                    'label' => 'Account Link',
                                    'uri'   => route('line.create', ['linkToken' => $response_body->linkToken]),
                                ],
                            ],
                        ],
                    ]
                ]
            ];

            $response = $client->request('POST', config('line.message_push'), [
                'headers' => $headers,
                'body'    => $request_body
            ]);

            if($response->getStatusCode() !== 200){
                abort(404);
            }
        }

        if(!$line_account->is_enable){
            $line_account->is_enable = true;
            $line_account->save();
        }
    }

    public function unfollowEvent($request_data)
    {
        $line_user_id = $request_data->events[0]->source->userId;

        LineAccount::query()
            ->where('line_user_id', $line_user_id)
            ->update([
                'is_enable' => false
            ]);
    }

    public function create(Request $request)
    {
        $request->query('linkToken');
        return view('line.create');
    }
}
