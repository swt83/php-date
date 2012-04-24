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
			// return formatted value
			return strftime($str, $this->time);
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
		
		// catch error
		if ($difference < 0) $difference = 0;
		
		// do math
		for($j = 0; $difference >= $lengths[$j] and $j < count($lengths)-1; $j++)
		{
			$difference /= $lengths[$j];
		}
		
		// round difference
		$difference = round($difference);
		
		// determine plural
		if($difference !== 1)
		{
			$periods[$j] .= 's';
		}
		
		// return
		return number_format($difference).' '.$periods[$j].' ago';
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