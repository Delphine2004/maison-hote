<?php

namespace App\Enum;

enum BookingStatus: string
{
    case CONFIRMED = "Confirmée";
    case CANCELLED = "Annulé";
    case FINALIZED = "Finalisé";
}
