<?php

namespace App\Enum;

enum BookingStatus: string
{
    case CONFIRMED = "Confirmee";
    case CANCELLED = "Annulee";
    case IN = "Present";
    case FINALIZED = "Finalisee";
    case OUTOFORDER = "Hors Service";
}
