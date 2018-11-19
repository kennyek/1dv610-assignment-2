<?php

class DateTimeView
{
    /** @var DateTime */
    private $currentDateTime;

    public function __construct(string $timeZone = 'Europe/Stockholm')
    {
        $dateTimeZone = new DateTimeZone($timeZone);
        $this->currentDateTime = new DateTime('now', $dateTimeZone);
    }

    public function getHtml(): string
    {
        return "<p>" . $this->getTimeDescription() . "</p>";
    }

    private function getTimeDescription(): string
    {
        $dayOfWeek = $this->getDayOfWeek();
        $dayOfMonth = $this->getDayOfMonth();
        $ordinal = $this->getOrdinal($dayOfMonth);
        $monthName = $this->getMonthName();
        $year = $this->getYear();

        $hour = $this->getHour();
        $minute = $this->getMinute();
        $second = $this->getSecond();

        return
            "$dayOfWeek, the $dayOfMonth" . "$ordinal of $monthName $year, " .
            "The time is $hour:$minute:$second";
    }

    private function getDayOfWeek(): string
    {
        return $this->currentDateTime->format('l');
    }

    private function getDayOfMonth(): string
    {
        return $this->currentDateTime->format('j');
    }

    private function getOrdinal(int $dayOfMonth): string
    {
        switch ($dayOfMonth) {
            case 1:
            case 21:
            case 31:
                return 'st';
            case 2:
            case 22:
                return 'nd';
            case 3:
            case 23:
                return 'rd';
            default:
                return 'th';
        }
    }

    private function getMonthName(): string
    {
        return $this->currentDateTime->format('F');
    }

    private function getYear(): string
    {
        return $this->currentDateTime->format('Y');
    }

    private function getHour(): string
    {
        return $this->currentDateTime->format('H');
    }

    private function getMinute(): string
    {
        return $this->currentDateTime->format('i');
    }

    private function getSecond(): string
    {
        return $this->currentDateTime->format('s');
    }
}
