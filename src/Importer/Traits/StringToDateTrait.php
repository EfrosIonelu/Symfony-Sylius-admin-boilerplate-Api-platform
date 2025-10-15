<?php

namespace App\Importer\Traits;

use PhpOffice\PhpSpreadsheet\Shared\Date;

trait StringToDateTrait // @phpstan-ignore-line
{
    protected function parseDateTime(?string $date): ?\DateTime
    {
        if (null === $date) {
            return null;
        }

        if (is_numeric($date)) {
            return Date::excelToDateTimeObject($date);
        }

        $formats = ['Y-m-d', 'd-M-Y', 'd-m-Y', 'd.m.Y', 'd/m/Y', 'Y/m/d', 'm/d/Y', 'Y/d/m'];
        $newDate = $date;

        foreach ($formats as $itemFormat) {
            $dateTime = \DateTime::createFromFormat($itemFormat, $newDate);

            if (false === $dateTime) {
                continue;
            }

            return $dateTime;
        }

        return null;
    }

    public function parseDate(?string $date): ?\DateTime
    {
        $newDate = $this->parseDateTime($date);
        if (null === $newDate) {
            return null;
        }

        $newDate->setTime(0, 0);

        return $newDate;
    }
}
