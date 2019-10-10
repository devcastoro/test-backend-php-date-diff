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

    public function isLeapYear(string $date): bool
    {
        /**
         * Check if date is a leap year
         */

        $subDate = explode("/", $date); // 0 -> YYYY 1 -> MM 2 -> DD
        $y = $subDate[0];
        $m = $subDate[1];

        // Check if the passed date is in february
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

        return false;
    }

    public function elaborateDaysInMonth($month, $year): int {

        // a standard short-month have 30 days
        $daysInMonth = 30;

        // a standard long-month have 31 days
        if (in_array($month, $this->longMonths)){
            $daysInMonth = 31;
        }

        // if month = february, check if is leap year (29) or not (28)
        elseif($month == '02'){

            if ($this->isLeapYear($year.'/02/01')) {
                $daysInMonth = 29;
            }
            else {
                $daysInMonth = 28;
            }
        }

        return $daysInMonth;
    }

    public function diff(): object
    {
        /**
        *
        * RETURN DIFF OBJECT
        *
        */

        $start = $this->getSplitDate($this->getStartDate());
        $end   = $this->getSplitDate($this->getEndDate());
        $inverted = 0;
        $daysDiff = [
            'y' => 0,
            'm' => 0,
            'd' => 0,
            'month' => 0,
        ];

        // check if start > of end, and invert
        if ($start['y'] > $end['y']) {
            $start = $this->getSplitDate($this->getEndDate());
            $end   = $this->getSplitDate($this->getStartDate());
            $inverted = 1;
        }
        else if ($start['y'] == $end['y'] && $start['m'] > $end['m']) {
            $start = $this->getSplitDate($this->getEndDate());
            $end   = $this->getSplitDate($this->getStartDate());
            $inverted = 1;
        }
        else if ($start['y'] == $end['y'] && $start['m'] > $end['m'] && $start['d'] > $end['d']) {
            $start = $this->getSplitDate($this->getEndDate());
            $end   = $this->getSplitDate($this->getStartDate());
            $inverted = 1;
        }

        // year diff: add days foreach year and month until the end date is reached
        $year = $start['y'];

        // for each year
        for ($c = $year; $c <= $end['y']; $c++) {

            // for each month of the year
            for ($i = ($c == $year ? $start['m'] : 1) ; $i <= 12; $i++) {

                // if is same MM and YY of start date, add only remaining days
                if ($i == $start['m'] && $c == $start['y']) {
                    $daysDiff['y'] +=  ($this->elaborateDaysInMonth($i, $c) - $start['d']);
                }

                // if is same MM and YY of end date, add only specified days
                else if ($i == $end['m'] && $c == $end['y']) {
                    $daysDiff['y'] +=  $end['d'];
                    break;
                }

                // if is not an end/start month, add all month's days
                else {
                    $daysDiff['y'] +=  $this->elaborateDaysInMonth($i, $c);
                }
            }
        }

        // day diff: adding a day until the end day date is reached
        for ($i = $start['d']; $i != $end['d']; $i++) {
            $daysDiff['d']++;
            if ($i == 31) {
                $i = 1;
            }
        }

        // Calculate differences
        $years  = intdiv($daysDiff['y'],365);
        $months = intdiv($daysDiff['y'] - (365*$years),30);
        $days   = $daysDiff['d'] ;

        return (object) array(
            'years'      => $years,
            'months'     => $months,
            'days'       => $days,
            'total_days' => $daysDiff['y'],
            'invert'     => $inverted
        );
    }
}