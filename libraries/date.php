<?php

/**
 * A LaravelPHP helper class for working w/ dates.
 *
 * @package    Date
 * @author     Scott Travis <scott.w.travis@gmail.com>
 * @link       http://github.com/swt83/laravel-date
 * @license    MIT License
 */

class Date
{
	protected $time;
	
	protected $formats = array(
		'datetime' => '%Y-%m-%d %H:%M:%S',
		'date' => '%Y-%m-%d',
		'time' => '%H:%M:%S',
	);
	
	public static function forge($str = null)
	{
		$class = __CLASS__;
		return new $class($str);
	}
	
	public function __construct($str = null)
	{
		// if no given...
		if ($str === null)
		{
			// use now
			$this->time = time();
		}
		
		// if given...
		else
		{
			// if number...
			if (is_numeric($str))
			{
				// treat as unix time
				$this->time = $str;
			}
			
			// if NOT number...
			else
			{
				// treat as string
				$time = strtotime($str);
				
				// if conversion fails...
				if (!$time)
				{
					// set time as false
					$this->time = false;
				}
				else
				{
					// accept time value
					$this->time = $time;
				}
			}
		}
	}

        // workaround for %V format on win32 platforms    
        // source from http://www.php.net/manual/en/function.strftime.php
        private function week_isonumber ($time) {
        // When strftime("%V") fails, some unoptimized workaround
        //
        // http://en.wikipedia.org/wiki/ISO_8601 : week 1 is "the week with the year's first Thursday in it (the formal ISO definition)"

            $year = strftime("%Y", $time);

            $first_day = strftime("%w", mktime(0, 0, 0, 1, 1, $year));
            $last_day = strftime("%w", mktime(0, 0, 0, 12, 31, $year));

            $number = $isonumber = strftime("%W", $time);

            // According to strftime("%W"), 1st of january is in week 1 if and only if it is a monday
            if ($first_day == 1)
                $isonumber--;

            // 1st of january is between monday and thursday; starting (now) at 0 when it should be 1
            if ($first_day >= 1 && $first_day <= 4)
                $isonumber++;
            else if ($number == 0)
                $isonumber = week_isonumber(mktime(0, 0, 0, 12, 31, $year - 1));

            if ($isonumber == 53 && ($last_day == 1 || $last_day == 2 || $last_day == 3))
                $isonumber = 1;

            return sprintf("%02d", $isonumber);
        } 
        
        // workaround for some formats on win32 platforms
        // source from http://www.php.net/manual/en/function.strftime.php
        private function strftime_win32($format, $ts = null) {
            if (!$ts) $ts = time();

            $mapping = array(
                '%C' => sprintf("%02d", date("Y", $ts) / 100),
                '%D' => '%m/%d/%y',
                '%e' => sprintf("%' 2d", date("j", $ts)),
                '%h' => '%b',
                '%n' => "\n",
                '%r' => date("h:i:s", $ts) . " %p",
                '%R' => date("H:i", $ts),
                '%t' => "\t",
                '%T' => '%H:%M:%S',
                '%u' => ($w = date("w", $ts)) ? $w : 7,
                '%V' => $this->week_isonumber ($ts)
            );
            $format = str_replace(
                array_keys($mapping),
                array_values($mapping),
                $format
            );

            return strftime($format, $ts);
        }	
        
	public function time()
	{
		return $this->time;
	}
        
	public function format($str)
	{
		// convert alias string
		if (in_array($str, array_keys($this->formats)))
		{
			$str = $this->formats[$str];
		}
	
		// if valid unix timestamp...
		if ($this->time !== false)
		{
                    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			// return formatted value on win32 platforms
			return $this->strftime_win32($str, $this->time);
                    } else {
			// return formatted value
			return strftime($str, $this->time);
                    }                    
		}
		else
		{
			// return false
			return false;
		}
	}
	
	public function reforge($str)
	{
		// if not false...
		if ($this->time !== false)
		{
			// amend the time
			$time = strtotime($str, $this->time);
			
			// if conversion fails...
			if (!$time)
			{
				// set time as false
				$this->time = false;
			}
			else
			{
				// accept time value
				$this->time = $time;
			}
		}
		
		// return
		return $this;
	}
	
	public function ago()
	{
		// set now and then
		$now = time();
		$time = $this->time();
		
		// catch error
		if (!$time) return false;
		
		// build period and length arrays
		$periods = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade');
		$lengths = array(60, 60, 24, 7, 4.35, 12, 10);
		
		// get difference
		$difference = $now - $time;
		
		// set descriptor
		if ($difference < 0)
		{
			$difference = abs($difference); // absolute value
			$negative = true;
		}
		
		// do math
		for($j = 0; $difference >= $lengths[$j] and $j < count($lengths)-1; $j++)
		{
			$difference /= $lengths[$j];
		}
		
		// round difference
		$difference = intval(round($difference));
		
		// determine plural
		if($difference !== 1)
		{
			$periods[$j] .= 's';
		}
		
		// return
		return number_format($difference).' '.$periods[$j].' '.(isset($negative) ? '' : 'ago');
	}
	
	public function until()
	{
		return $this->ago();
	}
	
	public static function diff($date1, $date2 = null)
	{
		// convert to objects, all
		if (!is_object($date1)) $date1 = self::forge($date1);
		if (!is_object($date2)) $date2 = self::forge($date2);
		
		// catch error
		if (!$date1->time() or !$date2->time()) return false;
		
		// perform comparison
		$date1 = date_create($date1->format('datetime'));
		$date2 = date_create($date2->format('datetime'));
		$diff = date_diff($date1, $date2);
		
		// catch error
		if ($diff === false) return false;
		
		// return
		return $diff;
	}
	
	public static function days_in_month($date)
	{
		// convert to object
		if (!is_object($date)) $date = self::forge($date);
	
		// return
		return cal_days_in_month(CAL_GREGORIAN, $date->format('%m'), $date->format('%Y'));
	}
}
