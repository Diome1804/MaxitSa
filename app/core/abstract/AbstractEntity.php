<?php

namespace App\Maxit\Core;

abstract class AbstractEntity
{


    abstract public static function toObject(Array $tableau):static;

    
    abstract public function toArray(Object $object):array;

    
     public function toJson(Object $object):string{

     }
}
