<?php

declare(strict_types = 1);

namespace Subito\Models;

use Subito\Interfaces\SubitoDateInterface;

class SubitoDateModel implements SubitoDateInterface
{
    private $startDate;
    private $endDate;

    public function __construct(string $startDate, string $endDate)
    {
        $this->setStartDate($startDate);
        $this->setEndDate($endDate);
    }

    public function setStartDate(string $date): void
    {
        if (!$this->isValidDate($date)) {
            throw new Exception('Start date is not a valid date');
        }

        $this->startDate = $date;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function setEndDate(string $date): void
    {
        if (!$this->isValidDate($date)) {
            throw new Exception('End date is not a valid date');
        }

        $this->endDate = $date;
    }

    public function getEndDate(): string
    {
        return $this->endDate;
    }

    public function isValidDate($date): bool
    {
        /* @TODO
        *
        * CHECK IF DATE IS VALID
        * RETURN TRUE OR FALSE
        *
        */

        return true;
    }

    public function diff(): object
    {
        /* @TODO
        *
        * RETURN DIFF OBJECT
        *
        */

        return (object) array(
            'years' => null,
            'months' => null,
            'days' => null,
            'total_days' => null,
            'invert' => null
        );
    }
}
