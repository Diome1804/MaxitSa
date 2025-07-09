<?php

namespace App\Entity;

class Compte
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

    // Getters / Setters...
}
