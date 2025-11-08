<?php

namespace App\Enum;

enum RoomStatus: string
{
    case AVAILABLE = "Disponible";
    case OUTOFORDER = "Hors Service";
    case BOOKED = "Réservée";
}
