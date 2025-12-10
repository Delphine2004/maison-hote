<?php

namespace App\Enum;

enum UserRole: string
{
    case ADMIN = "ROLE_EMPLOYE";
    case EMPLOYE = "ROLE_ADMIN";
    case CLIENT = "ROLE_CLIENT";
}
