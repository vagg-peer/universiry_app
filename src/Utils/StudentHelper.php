<?php

namespace App\Utils;

use DateTime;

class StudentHelper
{
    /**
     * Calculate the current semester of a student based on their start date.
     */
    public static function calculateStudentSemester(DateTime $startDate, ?DateTime $currentDate = null): int
    {
        if (!$currentDate) {
            $currentDate = new DateTime(); // Use today's date if not provided
        }

        // Get the difference in months
        $diffInMonths = ($startDate->diff($currentDate)->y * 12) + $startDate->diff($currentDate)->m;

        // Calculate the semester number (assuming 6 months per semester)
        return (int) floor($diffInMonths / 6) + 1;
    }
}