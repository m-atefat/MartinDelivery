<?php

namespace App\Enums;

use App\Traits\EnumToArray;

enum OrderStatusesEnum: string
{
    use EnumToArray;

    case PENDING = 'pending';
    case ACCEPT = 'accept';
    case CANCEL = 'cancel';

    case GOING_TO_SOURCE_LOCATION = 'going_to_source_location';
    case GOING_TO_DESTINATION_LOCATION = 'going_to_destination_location';
    case DONE = 'done';
}
