<?php

namespace App;

use DateTime;
use DateTimeZone;
use Exception;

class Utilities
{
    /**
     * @throws Exception
     */
    static function getCurrentTimeWithMilliseconds($format = null, $timezone = null): string
    {
        if ($format === null) {
            $format = 'Y-m-d H:i:s';
        }

        if ($timezone === null) {
            $timezone = new DateTimeZone(date_default_timezone_get());
        }
        // Get the current time with microseconds precision
        $time = self::getCurrentUnixTimeWithMilliseconds();

        // Convert the time to a DateTime object
        $datetime = DateTime::createFromFormat('U.u', $time, $timezone);
        if ($datetime) {
            // Format the DateTime object to include milliseconds with 3 decimals
            $milliseconds = $datetime->format('u') / 1000;
            return $datetime->format($format) . sprintf('.%03d', $milliseconds);
        } else {
            $bt = debug_backtrace();
            $caller = array_shift($bt);

            return date("$format.u");
        }
    }

    static function getCurrentUnixTimeWithMilliseconds(): string
    {
        return sprintf("%.3f", microtime(true));
    }
}