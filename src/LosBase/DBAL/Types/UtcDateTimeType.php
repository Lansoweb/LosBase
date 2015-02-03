<?php
namespace LosBase\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

class UtcDateTimeType extends DateTimeType
{
    /**
      * @param DateTime $value
      * @param Doctrine\DBAL\Platforms\AbstractPlatform $platform
      * @return string
      */
     public function convertToDatabaseValue($value, AbstractPlatform $platform)
     {
         if ($value === null) {
             return;
         }
         $formatString = $platform->getDateTimeFormatString();

         $formatted = $value->setTimezone(new \DateTimeZone('UTC'))->format($formatString);

         return $formatted;
     }

    /**
     * @param  string                                $value
     * @param  DoctrineDBALPlatformsAbstractPlatform $platform
     * @return DateTime|mixed|null
     * @throws DoctrineDBALTypesConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return;
        }

        $val = \DateTime::createFromFormat(
            $platform->getDateTimeFormatString(),
            $value,
            new \DateTimeZone('UTC')
        );
        if (!$val) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }

        return $val;
    }
}
