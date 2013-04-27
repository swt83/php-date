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
    /**
     * Object timestamp.
     *
     * @var     int
     */
    protected $time;
    
    /**
     * Pre-written formats.
     *
     * @var     array
     */
    protected $formats = array(
        'datetime' => '%Y-%m-%d %H:%M:%S',
        'date' => '%Y-%m-%d',
        'time' => '%H:%M:%S',
    );
    
    /**
     * Forge the date object.
     *
     * @param   string  $str
     * @return  object
     */
    public static function forge($str = null)
    {
        $class = __CLASS__;
        return new $class($str);
    }
    
    /**
     * Forge the date object.
     *
     * @param   string  $str
     * @return  object
     */
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
    
    /**
     * Return the object timestamp.
     *
     * @return  int
     */
    public function time()
    {
        return $this->time;
    }
    
    /**
     * Return the current date value in desired format.
     *
     * @param   string  $str
     * @return  string
     */
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
            // if on windows...
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
            {
                // return win32 formatted value
                return static::win32_strftime($str, $this->time);
            }

            // else if NOT windows...
            else
            {
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
    
    /**
     * Reforge the current date object.
     *
     * @param   string  $str
     * @return  object
     */
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
    
    /**
     * Return string of ago value based on current date and time.
     *
     * @return  string
     */
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
    
    /**
     * Alias of ago() method.
     *
     * @return  string
     */
    public function until()
    {
        return $this->ago();
    }
    
    /**
     * Return date diff object comparing two dates.
     *
     * @param   mixed   $date1
     * @param   mixed   $date2
     * @return  object
     */
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
    
    /**
     * Return number of days in month from given date.
     *
     * @param   mixed   $date
     * @return  int
     */
    public static function days_in_month($date)
    {
        // convert to object
        if (!is_object($date)) $date = self::forge($date);
    
        // return
        return cal_days_in_month(CAL_GREGORIAN, $date->format('%m'), $date->format('%Y'));
    }

    /**
     * Fix the strftime() function to work on win32 systems [CREDIT: mcpan68].
     *
     * @param   string  $format
     * @param   int     $time
     * @return  string
     */
    protected static function win32_strftime($format, $time = null)
    {
        // time
        if (!$time) $time = time();

        // map
        $map = array(
            '%C' => sprintf('%02d', date('Y', $time) / 100),
            '%D' => '%m/%d/%y',
            '%e' => sprintf("%' 2d", date('j', $time)),
            '%h' => '%b',
            '%n' => '\n',
            '%r' => date('h:i:s', $time) . ' %p',
            '%R' => date('H:i', $time),
            '%t' => '\t',
            '%T' => '%H:%M:%S',
            '%u' => ($w = date('w', $time)) ? $w : 7,
            '%V' => static::win32_v($time)
        );

        // replace
        $format = str_replace(array_keys($map), array_values($map), $format);

        // return
        return strftime($format, $time);
    }

    /**
     * Fix the strftime %V value to work on win32 systems [CREDIT: mcpan68].
     *
     * @param   int     $time
     * @return  int
     */
    protected static function win32_v($time)
    {
        $year = strftime('%Y', $time);

        $first_day = strftime('%w', mktime(0, 0, 0, 1, 1, $year));
        $last_day = strftime('%w', mktime(0, 0, 0, 12, 31, $year));

        $number = $isonumber = strftime('%W', $time);

        if ($first_day == 1)
        {
            $isonumber--;
        }

        if ($first_day >= 1 and $first_day <= 4)
        {
            $isonumber++;
        }
        elseif ($number == 0)
        {
            $isonumber = win32_v(mktime(0, 0, 0, 12, 31, $year - 1));
        }

        if ($isonumber == 53 and ($last_day == 1 or $last_day == 2 or $last_day == 3))
        {
            $isonumber = 1;
        }

        // return
        return sprintf('%02d', $isonumber);
    }
}