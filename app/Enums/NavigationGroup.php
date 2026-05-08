<?php

namespace App\Enums;

enum NavigationGroup: string
{
    case UserManagement = 'User Management';
    case Catalogue      = 'Catalogue';
    case Orders         = 'Orders';
    case Website        = 'Website';
}
