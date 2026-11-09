<?php

/*
| Site settings. Values come from .env so they can differ between your PC and Hostinger.
*/
return [
    'social' => [
        'YouTube' => env('ANU_YOUTUBE'),
        'Instagram' => env('ANU_INSTAGRAM'),
        'Facebook' => env('ANU_FACEBOOK'),
    ],
    'email' => env('ANU_EMAIL', 'hello@yourdomain.com'),
];
