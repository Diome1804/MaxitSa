<?php

use App\Core\Middlewares\Auth;
use App\Core\Middlewares\CryptPassword;

return [
    "auth" => Auth::class,
    "crypt" => CryptPassword::class
];