<?php
namespace LosBase\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DecimalType;

class BrPriceType extends DecimalType
{
    /**
     * @param  string|int|float|null                    $value
     * @param  Doctrine\DBAL\Platforms\AbstractPlatform $platform
     * @return mixed
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return;
        }

        $formatter = new \NumberFormatter('pt_BR', \NumberFormatter::DECIMAL);
        $formatted = $formatter->parse($value);

        return $formatted;
    }

    /**
     * @param  string                                $value
     * @param  DoctrineDBALPlatformsAbstractPlatform $platform
     * @return string|null
     * @throws DoctrineDBALTypesConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return;
        }
        $formatted = \number_format($value, 2, ',', '.');

        return $formatted;
    }

    /**
     * {@inheritdoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $fieldDeclaration['precision'] = (! isset($fieldDeclaration['precision']) || empty($fieldDeclaration['precision'])) ? 9 : $fieldDeclaration['precision'];
        $fieldDeclaration['scale'] = (! isset($fieldDeclaration['scale']) || empty($fieldDeclaration['scale'])) ? 2 : $fieldDeclaration['scale'];

        return $platform->getDecimalTypeDeclarationSQL($fieldDeclaration);
    }
}
