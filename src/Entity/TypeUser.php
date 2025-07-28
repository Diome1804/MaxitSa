<?php

namespace Src\Entity;

class TypeUser
{
    private int $id;
    private string $type; // client | serviceCom

    public function __construct(string $type)
    {
        $this->type = $type;
        // Pour l'instant, on définit un ID par défaut
        // Vous devrez adapter selon votre logique métier
        $this->id = $type === 'client' ? 1 : 2;
    }

    public function getId(): int { return $this->id; }
    public function getType(): string { return $this->type; }
    public function setType(string $type): void { $this->type = $type; }

    // Méthode pour définir l'ID (utile lors de l'hydratation)
    public function setId(int $id): void { $this->id = $id; }
}
