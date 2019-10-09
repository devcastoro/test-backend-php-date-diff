<?php

namespace Subito\Interfaces;

interface SubitoDateInterface
{
    public function setStartDate(string $date): void;

    public function getStartDate(): string;

    public function setEndDate(string $date): void;

    public function getEndDate(): string;

    public function getSplitDate(string $date): array;

    public function isValidDate(string $date): bool;

    public function isLeapYear(string $date): bool;

    public function elaborateDaysInMonth($month, $year): int;

    public function diff(): object;
}
