<?php

namespace App\DoctrineType;

enum TripMissing: string
{
    case Driver =  'driver';
    case Passenger =  'passenger';
}
