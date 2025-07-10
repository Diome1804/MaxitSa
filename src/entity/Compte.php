<?php

namespace App\Entity;
use App\Core\abstract\AbstractEntity;

class Compte extends AbstractEntity
{
    private int $id;
    private string $numCompte;
    private float $solde;
    private string $type; // ComptePrincipal | CompteSecondaire
    private User $user;
    private array $transactions = [];

    public function __construct(string $numCompte, float $solde, string $type, User $user)
    {
        $this->numCompte = $numCompte;
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


          public static function toObject(array $tableau): static
    {
        return new static(
            $tableau['id'] ?? 0,
            $tableau['num_compte'],
            (float)$tableau['solde'],
            $tableau['type'],
            $tableau['user_id'],
            $tableau['transactions'] ?? [],
            

        );
    }

    public function toArray(object $object): array
    {
        return [
            'id' => $this->id,
            'num_compte' => $this->numCompte,
            'solde' => $this->solde,
            'type' => $this->type,
            'user_id' => $this->user->getId(),
            'transactions' => $this->transactions,
        ];
    }

    
}
