<?php

declare(strict_types=1);

namespace App\Enum\Application;

/**
 * Class ApplicationInternalStatusEnum
 * @package App\Enum\Application
 */
enum ApplicationInternalStatusEnum: string
{
    case TYPE_CHOSEN = 'Wybrano rodzaj wniosku';
//    case STEP_2 = 'Zakończony przez studenta';
//    case STEP_3 = 'Wypożyczenie sprzętu specjalistycznego';
//    case STEP_4 = 'Organizacja wsparcia asystenta dydaktycznego';
}
