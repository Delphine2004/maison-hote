<?php

namespace App\Enum;

enum ReviewStatus: string
{
    case PENDING = "En attente";
    case CONFIRMED = "Confirme";
    case REJECTED = "Rejete";
}
