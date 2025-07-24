<?php

namespace Src\Entity;
use App\Core\abstract\AbstractEntity;

class Compte extends AbstractEntity
{
    private int $id;
    private string $numCompte;
    private string $dateCreation;
    private int $num_telephone;
    private float $solde;
    private string $type; // ComptePrincipal | CompteSecondaire
    private User $user;
    private array $transactions = [];

    public function __construct(string $numCompte, float $solde, string $type, User $user,)
    {
        $this->numCompte = $numCompte;
        $this->dateCreation = date('Y-m-d');
        $this->solde = $solde;
        $this->type = $type;
        $this->user = $user;
    }

    public function getTransactions(): array { return $this->transactions; }
    public function addTransaction(Transactions $transaction): void {
        $this->transactions[] = $transaction;
    }

    public function getUser(): User { return $this->user; }
    public function setUser(User $user): void { $this->user = $user; }



    // Ajoutez ces méthodes à votre classe Compte
    public function getId(): int { return $this->id; }
    public function getNumCompte(): string { return $this->numCompte; }
    public function getNumTelephone(): int { return $this->num_telephone; }
    public function getSolde(): float { return $this->solde; }
    public function getType(): string { return $this->type; }


   public static function toObject(array $tableau): static
{
    // Il faut d'abord récupérer l'utilisateur (ou créer un utilisateur temporaire)
    // Pour l'instant, on va créer un utilisateur basique
    $typeUser = new TypeUser('client');
    $user = new User('', '', $typeUser, '', '', '', '', '', '');
    
    // Si on a l'user_id, on pourrait le charger depuis la base
    // Mais pour simplifier, on va juste définir l'ID
    if (isset($tableau['user_id'])) {
        $reflection = new \ReflectionClass($user);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($user, $tableau['user_id']);
    }
    
    $compte = new static(
        $tableau['num_compte'] ?? '',
        (float)($tableau['solde'] ?? 0),
        $tableau['type'] ?? 'ComptePrincipal',
        $user
    );
    
    // Définir l'ID du compte
    if (isset($tableau['id'])) {
        $reflection = new \ReflectionClass($compte);
        $idProperty = $reflection->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($compte, $tableau['id']);
    }
    
    // Définir le num_telephone
    if (isset($tableau['num_telephone'])) {
        $reflection = new \ReflectionClass($compte);
        $numTelProperty = $reflection->getProperty('num_telephone');
        $numTelProperty->setAccessible(true);
        $numTelProperty->setValue($compte, $tableau['num_telephone']);
    }
    
    return $compte;
}


    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'num_compte' => $this->numCompte,
            'telephone' => $this->num_telephone,
            'solde' => $this->solde,
            'type' => $this->type,
            'user_id' => $this->user->getId(),
            'transactions' => $this->transactions,
        ];
    }

    
}
