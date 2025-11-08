<?php

namespace App\Utils;

class RegexPatterns
{
    public const ONLY_TEXTE_REGEX = '/^[a-zA-ZÀ-ÿ\s\'-]{1,60}$/u';
    public const FREE_TEXT_REGEX = '/^[a-zA-ZÀ-ÿ0-9\s\'".,;:!?()-]{1,255}$/u';

    public const FRENCH_MOBILE_PHONE = '/^(?:\+33|0)[6-7](?:\s?\d{2}){4}$/';

    public const LOGIN = '/^[a-zA-Z0-9\s\-]{8,25}$/u';
    public const PASSWORD = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{12,}$/';
}
