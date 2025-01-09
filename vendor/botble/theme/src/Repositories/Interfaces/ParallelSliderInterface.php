<?php

namespace Botble\Theme\Repositories\Interfaces;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

interface ParallelSliderInterface extends RepositoryInterface
{
    public function getAll(): Collection;
}