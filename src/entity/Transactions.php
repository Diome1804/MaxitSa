<?php

namespace Src\Entity;

class Transactions extends AbstractEntity
{
    private int $id;
    private \DateTime $date;
    private float $montant;
    private string $type;
    private Compte $compte;

    public function __construct(\DateTime $date, float $montant, string $type)
    {
        $this->date = $date;
        $this->montant = $montant;
        $this->type = $type;
    }

    public function getCompte(): Compte { return $this->compte; }
    public function setCompte(Compte $compte): void { $this->compte = $compte; }

    public function getId(): int { return $this->id; }
    public function setId(int $id): void { $this->id = $id; }
    public function getDate(): \DateTime { return $this->date; }
    public function setDate(\DateTime $date): void { $this->date = $date; }
    public function getMontant(): float { return $this->montant; }
    public function setMontant(float $montant): void { $this->montant = $montant; }
    public function getType(): string { return $this->type; }
    public function setType(string $type): void { $this->type = $type; }

    public function toArray(): array
    {
        return [
            'id' => $this->id ?? null,
            'compte_id' => $this->compteId,
            'type' => $this->type,
            'montant' => $this->montant,
            'description' => $this->description,
            'date_transaction' => $this->dateTransaction,
            'statut' => $this->statut,
            'compte_destinataire' => $this->compteDestinataire
        ];
    }
    public function toObject(): object{}
}
