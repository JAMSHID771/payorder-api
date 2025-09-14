<?php

return [
    'from' => env('SMS_FROM', env('ESKIZ_FROM', '4546')),
    'templates' => [
        'register' => env('SMS_TEMPLATE_REGISTER', 'Afisha Market MCHJ Tasdiqlovchi kodni kiriting:{code}'),
        'login' => env('SMS_TEMPLATE_LOGIN', 'Afisha Market MCHJ Tasdiqlovchi kodni kiriting:{code}'),
        'resend' => env('SMS_TEMPLATE_RESEND', 'Afisha Market MCHJ Tasdiqlovchi kodni kiriting:{code}'),
        'change_phone' => env('SMS_TEMPLATE_CHANGE_PHONE', 'Afisha Market MCHJ Tasdiqlovchi kodni kiriting:{code}'),
    ],
];


