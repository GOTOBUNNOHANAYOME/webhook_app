<?php

return [
    'default'      => env('LINE_ENDPOINT'),
    'data'         => env('LINE_DATA_ENDPOINT'),
    'access_token' => env('access_token'),
    'message'      => env('LINE_MESSAGE_URL'),
    'get_profile'  => 'https://api.line.me/v2/bot/profile',
];