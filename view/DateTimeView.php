<?php

class DateTimeView
{
    /** Renders a paragraph describing current date and time. */
    public function show()
    {
        $currentDateTime = new DateTime('now');
        $dayOfWeek = $currentDateTime->format('l');
        $dayOfMonth = $currentDateTime->format('j');
        $monthAsText = $currentDateTime->format('F');
        $year = $currentDateTime->format('Y');
        $hour = $currentDateTime->format('H');
        $minutes = $currentDateTime->format('i');
        $seconds = $currentDateTime->format('s');

        $timeDescription =
            "$dayOfWeek, the $dayOfMonth" . "th of $monthAsText $year, " .
            "The time is $hour:$minutes:$seconds";

        return "<p>$timeDescription</p>";
    }
}
