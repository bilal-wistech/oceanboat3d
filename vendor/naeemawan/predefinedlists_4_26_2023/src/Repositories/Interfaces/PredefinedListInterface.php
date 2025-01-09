<?php

namespace NaeemAwan\PredefinedLists\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface PredefinedListInterface extends RepositoryInterface
{
    public function getAll(): Collection;
    
    public function filterProducts(array $params = []);
}