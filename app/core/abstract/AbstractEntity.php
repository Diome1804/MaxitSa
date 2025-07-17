<?php

namespace App\Core\Abstract;

abstract class AbstractEntity {

    //static pas besoin de objet pour l'utiliser on fait NomDeLaclass::toObject 


    abstract public static function toObject(array $data):static;

    abstract public function toArray();

    public  function toJson(){
        return json_encode($this->toArray());
     }
}

   