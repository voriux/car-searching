<?php

namespace App\Collector;

use App\Model\Car;

interface CollectorInterface
{
    /**
     * @return array|Car[]
     */
    public function get(): array;
}