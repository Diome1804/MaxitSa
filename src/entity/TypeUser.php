<?php

namespace App\Entity;

class TypeUser
{
    private int $id;
    private string $type; // client | serviceCom

    public function __construct(string $type)
    {
        $this->type = $type;
    }

    public function getId(): int { return $this->id; }
    public function getType(): string { return $this->type; }

    public function setType(string $type): void { $this->type = $type; }
}
