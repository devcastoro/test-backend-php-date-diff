<?php

declare(strict_types = 1);

namespace Subito\Models;

use Subito\Interfaces\SubitoDateInterface;

class SubitoDateModel implements SubitoDateInterface
{
    private $startDate;
    private $endDate;

    private $longMonths  = ['01','03','05','07','08','10','12'];
    private $shortMonths = ['02','04','06','09','11'];

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

    public function getSplitDate(string $date): array {

        $dateExploded = explode("/", $date); // 0 -> YYYY 1 -> MM 2 -> DD
        return [
            'fullDate' => $date,
            'y'        => $dateExploded[0],
            'm'        => $dateExploded[1],
            'd'        => $dateExploded[2],
        ];

    }

    public function isValidDate($date): bool
    {
        /**
        *
        * CHECK IF DATE IS VALID
        * RETURN TRUE OR FALSE
        *
        */

        // Check if date is a string
        if (!is_string($date)) {
            return false;
        }

        // Check if the string contain 2 slash
        if (substr_count($date, '/') !== 2) {
            return false;
        }

        // Check if the length is correct
        if (strlen($date) !== 10) {
            return false;
        }

        // Check if the YYYY format is correct in length and if contains only number
        $subDate = explode("/", $date); // 0 -> YYYY 1 -> MM 2 -> DD
        $y = $subDate[0];
        $m = $subDate[1];
        $d = $subDate[2];
        if (strlen($y) !== 4 || !ctype_digit($y)) {
            return false;
        }

        // Check if the MM format is correct in length, if contains only numbers, if is in the 1-12 range
        if (strlen($m) !== 2 || !ctype_digit($m) || $m < 1 || $m > 12) {
            return false;
        }

        // Check if the DD format is correct in length, if contains only numbers, if is in the 1-31 range
        if (strlen($d) !== 2 || !ctype_digit($d) || $d < 1 || $d > 31) {
            return false;
        }

        // Check if is a 30-days months and day is within 1-30 range
        if (in_array($m, $this->shortMonths) && $d > 30) {
            return false;
        }

        // Check February non-leap year
        if ($m === '02') {

            // Calculate if is a leap-year
            $leapYear = false;
            if ($y % 100 === 0 && $y % 400 !== 0) {
                $leapYear = false;
            }
            elseif ($y % 4 === 0)  {
                $leapYear = true;
            }

            // Check if non-leap year and date > 28
            if ($leapYear === false && $d > 28) {
                return false;
            }

            // Check if leap year and date > 29
            if ($leapYear === true && $d > 29) {
                return false;
            }
        }

        return true;
    }

    public function isLeapYear($date): bool
    {
        $subDate = explode("/", $date); // 0 -> YYYY 1 -> MM 2 -> DD
        $y = $subDate[0];
        $m = $subDate[1];
        $d = $subDate[2];

        // Check February non-leap year
        if ($m === '02') {

            // Calculate if is a leap-year
            $leapYear = false;
            if ($y % 100 === 0 && $y % 400 !== 0) {
                $leapYear = false;
            }
            elseif ($y % 4 === 0)  {
                $leapYear = true;
            }

            return $leapYear;
        }

        return true;
    }

    public function diff(): object
    {
        /* @TODO
        *
        * RETURN DIFF OBJECT
        *
        */

        $start = $this->getSplitDate($this->getStartDate());
        $end   = $this->getSplitDate($this->getEndDate());

        $differences = [];
        $differences['y'] = $end['y'] - $start['y'];
        $differences['m'] = $end['m'] - $start['m'];
        $differences['d'] = 0;

        // calculate day difference
        //$dayDifference = $start['d'] - $end['d'];
        $i = $start['d'];
        while ($i != $end['d']-1) {

            $differences['d'] = $differences['d'] + 1;

            if ($i == 31) {
                $i = 1;
            }
            else { $i++; }
        }


        // calculate month difference
        $daysMonthDifference = 0;
        $monthsDifference = $start['m'] - $end['m'];
        $referenceYear  = $start['y'];
        $referenceMonth = $start['m']-1;

        for ($i = 0; $i < abs($monthsDifference); $i++) {

            $daysInMonth = 30;
            if (in_array($referenceMonth, $this->longMonths)){
                $daysInMonth = 31;
            }
            elseif(in_array($referenceMonth, $this->shortMonths) && $referenceMonth == '02'){

                if ($this->isLeapYear($referenceYear.'/02/01')) {
                    $daysInMonth = 29;
                }
                else {
                    $daysInMonth = 28;
                }
            }

            $daysMonthDifference = $daysMonthDifference + $daysInMonth;

            $referenceMonth--;
            if ($referenceMonth === 0) {
                $referenceMonth = 12;
                $referenceYear--;
            }
        }

        // calculate year difference
        $daysInYearDifference = 0;
        $startingYear = $start['y'];
        if ($start['y'] < $end['y']) {
            for ($i = 1; $i <= 12; $i++) {

                $daysInMonth = 30;

                if (in_array($i, $this->longMonths)){
                    $daysInMonth = 31;
                }

                elseif(in_array($i, $this->shortMonths) && $i == '02'){

                    if ($this->isLeapYear($startingYear.'/02/01')) {
                        $daysInMonth = 29;
                    }
                    else {
                        $daysInMonth = 28;
                    }
                }

                $daysInYearDifference = $daysInYearDifference + $daysInMonth;
            }
        }

        return (object) array(
            'years'  => intdiv($daysInYearDifference , 365),
            'months' => intdiv($daysMonthDifference , 31),
            'days'   => ($differences['d']),
            'total_days' => $daysInYearDifference + $daysMonthDifference - ($start['d'] - $end['d']) +1,
            'invert' => null
        );
    }
}
