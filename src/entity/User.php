<?php
namespace App\Entity;

class User
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

    public function __construct(string $nom, string $prenom, TypeUser $type)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->type = $type;
    }

    // Getters / Setters...

    public function getComptes(): array { return $this->comptes; }
    public function addCompte(Compte $compte): void {
        $this->comptes[] = $compte;
    }

    public function getType(): TypeUser { return $this->type; }
    public function setType(TypeUser $type): void { $this->type = $type; }
}
