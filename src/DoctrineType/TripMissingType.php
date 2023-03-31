<?php

namespace App\DoctrineType;

use App\DoctrineType\AbstractEnumType;
use App\DoctrineType\TripMissing;

class TripMissingType extends AbstractEnumType
{
    public const NAME = 'tripMissing';

    public function getName(): string // the name of the type.
    {
        return self::NAME;
    }

    public static function getEnumsClass(): string // the enums class to convert
    {
        return TripMissing::class;
    }
}
