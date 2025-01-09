<?php

namespace NaeemAwan\PredefinedLists\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface PredefinedCategoryInterface extends RepositoryInterface
{
    public function getAll(): Collection;
}