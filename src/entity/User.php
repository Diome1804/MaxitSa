<?php
namespace App\Entity;
use App\Core\abstract\AbstractEntity;

class User extends AbstractEntity
{
    private int $id;
    private string $nom;
    private string $prenom;
    private string $adresse;
    private string $numCarteIdentite;
    private string $photorecto;
    private string $photoverso;
    private string $telephone;
    private string $password;
    private TypeUser $type;
    private array $comptes = [];

    public function __construct(string $nom, string $prenom, TypeUser $type , string $adresse, string $numCarteIdentite, string $photorecto, string $photoverso, string $telephone, string $password)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->type = $type;
        $this->adresse = $adresse;
        $this->numCarteIdentite = $numCarteIdentite;
        $this->photorecto = $photorecto;
        $this->photoverso = $photoverso;
        $this->telephone = $telephone;
        $this->password = $password;
    }

    

    public function getComptes(): array { return $this->comptes; }
    public function addCompte(Compte $compte): void {
        $this->comptes[] = $compte;
    }

    public function getType(): TypeUser {
         return $this->type; 
    }

    public function setType(TypeUser $type): void {
         $this->type = $type;
    }


          public static function toObject(array $tableau): static
    {
        return new static(
            $tableau['id'] ?? 0,
            $tableau['nom'] ?? '',
            $tableau['prenom'] ?? '',
            $tableau['adresse'] ?? '',
            $tableau['numCarteIdentite'] ?? '',
            $tableau['photorecto'] ?? '',
            $tableau['photoverso'] ?? '',
            $tableau['telephone'] ?? '',
            $tableau['password'] ?? '',
            $tableau['type'],
            $tableau['comptes'] ?? []
        );
    }

    public function toArray(object $object): array
    //public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'adresse' => $this->adresse,
            'numCarteIdentite' => $this->numCarteIdentite,
            'photorecto' => $this->photorecto,
            'photoverso' => $this->photoverso,
            'telephone' => $this->telephone,
            'password' => $this->password,
            'type' => $this->type,
            'comptes' => $this->comptes

        ];
    }


}
//pour faire lintanciation de la classe cest a dire pour creer un objetde tuppe user on le fais en dehors de la classe  



