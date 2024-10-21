<?php

namespace App\Enums\API\V1;

enum JobStatusEnum: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case IN_PROGRESS = 'in-progress';
}
