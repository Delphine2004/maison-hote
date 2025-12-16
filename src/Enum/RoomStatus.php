<?php

namespace App\Enum;

enum RoomStatus: string
{
    case AVAILABLE = "Disponible";
    case BOOKED = "Réservée";
}
