<?php

namespace App\Core\Interfaces;

interface RepositoryInterface
{
    public function insert(array $data): int|false;
    public function selectAll();
    public function selectById();
    public function selectBy(array $filter);
    public function update();
    public function delete();
}
