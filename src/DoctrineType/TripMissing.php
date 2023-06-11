<?php

namespace App\DoctrineType;

enum TripMissing: string
{
    case Driver =  'driver';
    case Passenger =  'passenger';


    /**
     * @return array<string,string>
     */
    public static function getAsArray(): array
    {
        return array_reduce(
            self::cases(),
            static fn (array $choices, TripMissing $type) =>  $choices + [$type->name => $type->value],
            [],
        );
    }

    /**
     * @return array<string,string>
     */
    public static function enumArray(): array
    {
        return array_reduce(
            self::cases(),
            static fn (array $choices, TripMissing $type) =>  $choices + [$type],
            [],
        );
    }
}
