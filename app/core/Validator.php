<?php

namespace App\Core;

class Validator
{
    private static array $errors = [];

    public static function isEmail(string $email): bool{
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            return true;
        }
        self::$errors['email_valid'] = "L'email n'est pas valide";
        return false;   
    }

    public static function isEmpty(string $value, string $field = 'field'): bool{
        if(empty($value)){
            self::$errors[$field . '_empty'] = "Le champ " . $field . " est vide";
            return true;
        }
        return false;
    }

    public static function minLength(string $value, int $minLength, string $field = 'field'): bool{
        if(strlen($value) < $minLength){
            self::$errors[$field . '_min_length'] = "Le champ " . $field . " doit contenir au moins " . $minLength . " caractères";
            return false;
        }
        return true;
    }

    public static function validatePassword(string $password): bool{
        $isValid = true;
        
        if(self::isEmpty($password, 'mot de passe')){
            $isValid = false;
        }
        
        if(!self::minLength($password, 6, 'mot de passe')){
            $isValid = false;
        }
        
        return $isValid;
    }

    public static function validateLogin(string $login): bool{
        $isValid = true;
        
        if(self::isEmpty($login, 'login')){
            $isValid = false;
        }
        
        if(!self::minLength($login, 3, 'login')){
            $isValid = false;
        }
        
        return $isValid;
    }

    public static function addError(string $key, string $message): void{
        self::$errors[$key] = $message;
    }

    public static function isValid(): bool{
        return count(self::$errors) === 0;
    }

    public static function getErrors(): array{
        return self::$errors;
    }

    public static function clearErrors(): void{
        self::$errors = [];
    }
}
