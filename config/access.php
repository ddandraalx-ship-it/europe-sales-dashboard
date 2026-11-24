<?php

return [
    'username' => env('ADMIN_USERNAME', env('APP_ENV') === 'local' ? 'admin' : null),
    'password' => env('ADMIN_PASSWORD', env('APP_ENV') === 'local' ? 'admin' : null),
];
