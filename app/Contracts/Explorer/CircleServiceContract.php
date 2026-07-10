<?php

namespace App\Contracts\Explorer;

use App\Models\Explorer\Circles\Circle;

interface CircleServiceContract
{
    public function boot(Circle $circle): void;

    public function getKey(): string;

    public function getPermissions(): array;
}
