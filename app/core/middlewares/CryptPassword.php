<?php

namespace App\Core\Middlewares;
class CryptPassword{

    public function __invoke($password){
        return password_hash($password, PASSWORD_DEFAULT);
    }

}