<?php

namespace App\Core\Interfaces;

interface ControllerInterface
{
    public function index();
    public function create();
    public function store();
    public function edit();
    public function show();
}
