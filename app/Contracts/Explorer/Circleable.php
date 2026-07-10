<?php

namespace App\Contracts\Explorer;

use App\Models\Explorer\Circles\Circle;
use Illuminate\Database\Eloquent\Relations\HasOne;

interface Circleable
{
    public function circle(): HasOne;

    public function hasService(string $serviceKey): bool;

    public function isNestedIn(Circle $circle): bool;
    public function getCircleName(): string;
    public function getCircleDescription(): string;
}
