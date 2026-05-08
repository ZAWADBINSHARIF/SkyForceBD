<?php

namespace App\Enums;

enum FieldLength: int
{
    case Tiny       = 50;    // names, slugs, short labels
    case Short      = 100;   // phone, email, card brand, country
    case Default    = 255;   // standard varchar
    case Long       = 500;   // links, URLs, purchase links
    case ExtraLong  = 1000;  // remarks, employee notes
}
