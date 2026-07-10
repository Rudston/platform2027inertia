<?php

namespace App\Enums\Explorer;

enum CircleStatus: string
{
    case Active    = 'active';
    case Pending   = 'pending';
    case Denied    = 'denied';
    case Suspended = 'suspended';
    case Archived  = 'archived';
}
