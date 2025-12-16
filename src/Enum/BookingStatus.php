<?php

namespace App\Enum;

enum BookingStatus: string
{
    case CONFIRMED = "Confirmée";
    case CANCELLED = "Annulé";
    case IN = "Present";
    case FINALIZED = "Finalisé";
    case OUTOFORDER = "Hors Service";
}
