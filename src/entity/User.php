<?php

namespace Src\Entity;

use App\Core\abstract\AbstractEntity;
use Src\Entity\Compte;
use App\Core\ReflectionFactory;

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

    public function __construct(?string $nom, ?string $prenom, TypeUser $type, ?string $adresse, ?string $numCarteIdentite, ?string $photorecto, ?string $photoverso, ?string $telephone, ?string $password)
    {
        $this->nom = $nom ?? '';
        $this->prenom = $prenom ?? '';
        $this->type = $type;
        $this->adresse = $adresse ?? '';
        $this->numCarteIdentite = $numCarteIdentite ?? '';
        $this->photorecto = $photorecto ?? '';
        $this->photoverso = $photoverso ?? '';
        $this->telephone = $telephone ?? '';
        $this->password = $password ?? '';
    }

    // Getters
    public function getComptes(): array { return $this->comptes; }

    public function addCompte(Compte $compte): void {
         $this->comptes[] = $compte;
         }

    public function getType(): TypeUser { return $this->type; }
    public function setType(TypeUser $type): void { $this->type = $type; }
    public function getId(): int { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }
    public function getAdresse(): string { return $this->adresse; }
    public function getNumCarteIdentite(): string { return $this->numCarteIdentite; }
    public function getPhotorecto(): string { return $this->photorecto; }
    public function getPhotoverso(): string { return $this->photoverso; }
    public function getTelephone(): string { return $this->telephone; }
    public function getPassword(): string { return $this->password; }

    public static function toObject(array $tableau): static
    {
        $factory = ReflectionFactory::getInstance();
        
        $typeUser = $factory->create(TypeUser::class, [$tableau['type'] ?? 'client']);
        $user = $factory->create(static::class, [
            $tableau['nom'] ?? '',
            $tableau['prenom'] ?? '',
            $typeUser,
            $tableau['adresse'] ?? '',
            $tableau['numCarteIdentite'] ?? '',
            $tableau['photorecto'] ?? '',
            $tableau['photoverso'] ?? '',
            $tableau['telephone'] ?? '',
            $tableau['password'] ?? ''
        ]);

        // var_dump($tableau);
        // die();  
        
        if (isset($tableau['id'])) {
            $reflection = new \ReflectionClass($user);
            $idProperty = $reflection->getProperty('id');
            $idProperty->setAccessible(true);
            $idProperty->setValue($user, $tableau['id']);
        }

        return $user;
    }

    
    public function toArray(): array
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
            'type' => $this->type->getType(),
            'comptes' => $this->comptes
        ];
    }
}
