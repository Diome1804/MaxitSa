<?php

namespace App\Core\abstract;

abstract class AbstractEntity
{

    //les fonction suivantes sont abstraites
    //donc  elles doivent être implémentées dans les classes qui héritent de cette classe abstraite
    //et chaque classe qui hérite de cette classe abstraite doit implémenter ces fonctions
    //de ca maniere sauf les function qui ne sont pas abstraite ici car elle sont concret donc on les definies ici comme la fonction toJson 


    abstract public static function toObject(Array $tableau):static;

    
    abstract public function toArray(Object $object):array;

    
     public function toJson(Object $object):string{

     }
}
