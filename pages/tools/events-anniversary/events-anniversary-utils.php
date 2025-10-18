<?php
    function eventsAnniversaryUser(){
        return $_SESSION['appUserId'];
    }

    function formatDisplayDate($date) {
        if (!$date) return '';
        return date('d M Y', strtotime($date));
    }

    function nextLeapYear($y) {
        $year = $y;
        while (true) {
            $year++;
            if ((($year % 4 == 0) && ($year % 100 != 0)) || ($year % 400 == 0)) {
                return $year;
            }
        }
    }

    function nextOccurrence($originalDate) {
        // Handles next occurrence based on today's date, including Feb 29
        try {
            $orig = new DateTime($originalDate);
            $today = new DateTime('today');

            $monthDay = $orig->format('m-d');
            $year = (int)$today->format('Y');

            if ($monthDay === '02-29') {
                // Find the next leap year on or after current year, but not in the past
                $target = new DateTime($year . '-02-29');
                if ($target < $today) {
                    $year = nextLeapYear($year);
                } else {
                    // If current year is not leap and we're before 29 Feb, next leap must be found
                    if (!checkdate(2, 29, $year)) {
                        $year = nextLeapYear($year);
                    }
                }
                return (new DateTime($year . '-02-29'))->format('Y-m-d');
            }

            $candidate = new DateTime($year . '-' . $monthDay);
            if ($candidate < $today) {
                $candidate->modify('+1 year');
            }
            return $candidate->format('Y-m-d');
        } catch (Exception $e) {
            return $originalDate;
        }
    }
?>
