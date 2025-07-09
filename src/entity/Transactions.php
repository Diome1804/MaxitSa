<?php

namespace App\Entity;

class Transactions
{
    private int $id;
    private \DateTime $date;
    private float $montant;
    private string $type; // paiement | transfert

    private Compte $compte;

    public function __construct(\DateTime $date, float $montant, string $type, Compte $compte)
    {
        $this->date = $date;
        $this->montant = $montant;
        $this->type = $type;
        $this->compte = $compte;
    }

    public function getCompte(): Compte { return $this->compte; }
    public function setCompte(Compte $compte): void { $this->compte = $compte; }

    // Getters / Setters...
}
