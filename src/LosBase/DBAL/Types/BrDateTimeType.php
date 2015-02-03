<?php
namespace LosBase\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;

class BrDateTimeType extends UtcDateTimeType
{
    /**
     * @param  string                                $value
     * @param  DoctrineDBALPlatformsAbstractPlatform $platform
     * @return DateTime|mixed|null
     * @throws DoctrineDBALTypesConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $val = parent::convertToPHPValue($value, $platform);

        return $val->setTimezone(new \DateTimeZone('America/Sao_Paulo'));
    }
}
