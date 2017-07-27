<?php

namespace Lib;

class DateTimeEx extends \DateTime {

    /** @return string Y-m-d H:i:s */
    function toIsoString($time = TRUE) {
        if ($time === TRUE) {
            return $this->format('Y-m-d H:i:s');
        } elseif ($time === FALSE) {
            return $this->format('Y-m-d');
        } else {
            return $this->format('Y-m-d') . " $time";
        }
    }

    /**
     * return time insert ts in cassandra
     * @return type
     */
    function toIsoString2() {
        return $this->format('Y-m-d') . "T" . $this->format('H:i:s') . $this->getLocalTimezone();
    }

    function getLocalTimezone() {
        $strTimezone = date_default_timezone_get();
        $offset = timezone_offset_get(new \DateTimeZone($strTimezone), new \DateTime());
        return $this->timezone_offset_string($offset);
    }

    function timezone_offset_string($offset) {
        return sprintf("%s%02d:%02d", ( $offset >= 0 ) ? '+' : '-', abs($offset / 3600), abs($offset % 3600));
    }

    /** @return string d/m/Y */
    function toShortdateString() {
        return $this->format('d/m/Y');
    }

    /** @return string H:i */
    function toTimeString() {
        return $this->format('H:i');
    }

    /** @return string d/m/Y */
    function toFulldateFtring() {
        return $this->format('d/m/Y H:i');
    }

    /** @return string t */
    function toMaxDayOfMonth() {
        return $this->format('t');
    }

    /**
     * Cộng/trừ ngày
     * @param int $int
     * @return static
     */
    function addDay($int) {
        $int = (int) $int;
        if ($int > 0) {
            return $this->add(new \DateInterval("P{$int}D"));
        } else {
            $int = -$int;
            return $this->sub(new \DateInterval("P{$int}D"));
        }
    }

    /**
     * Cộng/trừ tháng
     * @param int $int
     * @return static
     */
    function addMonth($int) {
        $int = (int) $int;
        if ($int > 0) {
            return $this->add(new \DateInterval("P{$int}M"));
        } else {
            $int = -$int;
            return $this->sub(new \DateInterval("P{$int}M"));
        }
    }

    /**
     * Cộng/trừ năm
     * @param int $int
     * @return static
     */
    function addYear($int) {
        $int = (int) $int;
        if ($int > 0) {
            return $this->add(new \DateInterval("P{$int}Y"));
        } else {
            $int = -$int;
            return $this->sub(new \DateInterval("P{$int}Y"));
        }
    }

    /**
     * Cộng/trừ giây
     * @param int $int
     * @return static
     */
    function addSecond($int) {
        $int = (int) $int;
        if ($int > 0) {
            return $this->add(new \DateInterval("PT{$int}S"));
        } else {
            $int = -$int;
            return $this->sub(new \DateInterval("PT{$int}S"));
        }
    }

    /**
     * Cộng/trừ giờ
     * @param int $int
     * @return static
     */
    function addHour($int) {
        $int = (int) $int;
        if ($int > 0) {
            return $this->add(new \DateInterval("PT{$int}H"));
        } else {
            $int = -$int;
            return $this->sub(new \DateInterval("PT{$int}H"));
        }
    }

    /**
     * Cộng/trừ phút
     * @param int $int
     * @return static
     */
    function addMinute($int) {
        $int = (int) $int;
        if ($int > 0) {
            return $this->add(new \DateInterval("PT{$int}M"));
        } else {
            $int = -$int;
            return $this->sub(new \DateInterval("PT{$int}M"));
        }
    }

    /**
     * 
     * @param type $format
     * @param type $time
     * @param type $object
     * @return static
     */
    static function createFromFormat($format, $time, $object = null) {
        $object = $object ? $object : new \DateTimeZone(date_default_timezone_get());
        $date = parent::createFromFormat($format, $time, $object);
        return $date ? new static($date->format('Y-m-d H:i')) : false;
    }

    /**
     * 
     * @param string $time d/m/Y | d-m-Y
     * @return static
     */
    static function createFrom_dmY($time) {
        $time = str_replace('-', '/', $time);
        $date = parent::createFromFormat('d/m/Y', $time);
        return $date ? new static($date->format('Y-m-d H:i')) : false;
    }

    /**
     * 
     * @param string $time d/m/Y H:i | d-m-Y H:i
     * @return static
     */
    static function createFrom_dmY_Hi($time) {
        $time = str_replace('-', '/', $time);
        $date = parent::createFromFormat('d/m/Y H:i', $time);
        return $date ? new static($date->format('Y-m-d H:i')) : false;
    }

    /**
     * 
     * @param string $time
     * @return static
     */
    static function create($time = null) {
        $time = $time == '0000-00-00 00:00:00' ? null : $time;
        return new static($time);
    }

    function get_vietnamese_day() {
        $map = array(
            1 => 'thứ 2',
            2 => 'thứ 3',
            3 => 'thứ 4',
            4 => 'thứ 5',
            5 => 'thứ 6',
            6 => 'thứ 7',
            7 => 'chủ nhật'
        );
        return $map[$this->format('N')];
    }

    function toIsoStringYmd($time = TRUE) {
        if ($time === TRUE) {
            return $this->format('Y-m-d');
        } elseif ($time === FALSE) {
            return $this->format('Y-m-d');
        } else {
            return $this->format('Y-m-d') . " $time";
        }
    }

}
