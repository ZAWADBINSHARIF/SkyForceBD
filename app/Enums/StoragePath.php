<?php

namespace App\Enums;

enum StoragePath: string
{
    case CustomerAvatar    = 'customers/avatars';
    case ProductImage      = 'products/images';
    case OfficeImage       = 'offices/images';
    case BannerImage       = 'banners';
    case TransactionProof  = 'transactions/proofs';
    case AboutImage        = 'about/images';
}
