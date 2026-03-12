<?php

return [

    'credentials' => [
        'grant_type' => 'client_credentials',
        'client_id' => env('IAM_CLIENT_ID' ?? '019ce196-0844-71af-8649-d6d2c1b7147b'),
        'client_secret' => env('IAM_CLIENT_SECRET' ?? "6nCH1sb0CTQd1SBHGRSK7fX9EJFDbdAz9rV9BtYp"),
        'redirect_uri' => env('IAM_REDIRECT_URI'),
        'scope' => '',
    ],
];
