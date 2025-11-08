<?php

namespace App\Enum;

enum UserRole: string
{
    case ADMIN = "Admin";
    case EMPLOYE = "Employé";
    case CLIENT = "Client";
}
