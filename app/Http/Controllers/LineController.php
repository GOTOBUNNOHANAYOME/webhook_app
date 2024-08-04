<?php

namespace App\Http\Controllers;

use App\Models\{
    LineAccount,
    LineAuthentication,
    User
};
use App\Enums\{
    LineRequestType,
    LineAccountStatus
};
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
                case LineRequestType::ACCOUNT_LINK:
                    $this->accountLinkEvent($request_data);
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

    private function followEvent($request_data)
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
                'status'        => LineAccountStatus::TEMPORARY,
                'is_enable'     => true
            ]);
        }

        if($line_account->status !== LineAccountStatus::CONNECTED){
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
            
            LineAuthentication::create([
                'line_account_id' => $line_account->id,
                'link_token'      => $response_body->linkToken
            ]);

            $request_body = [
                'to'       => $line_account->line_user_id,
                'messages' => [
                    [
                        'type'     => 'template',
                        'altText'  => 'ヌッコの民、' . $line_account->name . 'よ。リンクをクリックして認証を済ますやで。',
                        'template' => [
                            'type'    => 'buttons',
                            'text'    => 'ヌッコの民、' . $line_account->name . 'よ。リンクをクリックして認証を済ますやで。',
                            'actions' => [
                                [
                                    'type'  => 'uri',
                                    'label' => 'クリックするやで',
                                    'uri'   => route('line.redirect_account_link', ['linkToken' => $response_body->linkToken]),
                                ],
                            ],
                        ],
                    ]
                ]
            ];

            $response = $client->request('POST', config('line.message_push'), [
                'headers' => $headers,
                'json'    => $request_body
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

    private function unfollowEvent($request_data)
    {
        $line_user_id = $request_data->events[0]->source->userId;

        LineAccount::query()
            ->where('line_user_id', $line_user_id)
            ->update([
                'is_enable' => false
            ]);
    }

    private function accountLinkEvent($request_data)
    {
        $line_user_id = $request_data->events[0]->source->userId;
        $nonce = base64_decode($request_data->events[0]->link->nonce);
        $result = $request_data->events[0]->link->result;

        if($result !== 'ok'){
            abort(404);
        }

        $line_account = LineAccount::query()
            ->where('line_user_id', $line_user_id)
            ->whereHas('lineAuthentications', function($query) use ($nonce){
                return $query->where('nonce', $nonce);
            })
            ->first();
        
        $account_id = null;
        do{
            $account_id = Str::random(30);
        }while(User::where('account_id', $account_id)->exists());

        $user = User::create([
            'account_id' => $account_id,
            'is_enable'  => true
        ]);
        $line_account->status = LineAccountStatus::CONNECTED;
        $line_account->user_id = $user->id;
        $line_account->save();
    }

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
