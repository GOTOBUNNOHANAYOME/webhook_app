<?php

return [
    'default'      => env('LINE_ENDPOINT'),
    'data'         => env('LINE_DATA_ENDPOINT'),
    'access_token' => env('LINE_ACCESS_TOKEN'),
    'get_profile'  => 'https://api.line.me/v2/bot/profile',
    'account_link' => 'https://api.line.me/v2/bot/user',
    'message_push' => 'https://api.line.me/v2/bot/message/push',
    'link_nonce'   => 'https://access.line.me/dialog/bot/accountLink'
];