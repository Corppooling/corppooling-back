<?php

namespace App\DoctrineType;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

abstract class AbstractEnumType extends Type
{
    abstract public static function getEnumsClass(): string;

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'TEXT';
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        if ($value instanceof \BackedEnum) {
            return $value->value;
        }
        return null;
    }
    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        if (false === enum_exists($this->getEnumsClass(), true)) {
            throw new \LogicException("This class should be an enum");
        }
        // 🔥 https://www.php.net/manual/en/backedenum.tryfrom.php 
        return $this::getEnumsClass()::tryFrom($value);
    }
}
