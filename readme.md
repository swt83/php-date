# Date for LaravelPHP #

Working w/ dates can be a bear.  While this helper class probably is missing some things, I find it helpful.

## Install ##

In ``application/bundles.php`` add:

```php
'date' => array('auto' => true),
```

## Examples ##

Some basic examples:

```php
// default timestamp is now
$date = Date::forge();

// pass timestamps
$date = Date::forge(1333857600);

// pass strings
$date = Date::forge('last sunday');

// get timestamp
$date = Date::forge('last sunday')->time(); // 1333857600

// get a nice format
$date = Date::forge('last sunday')->format('%B %d, %Y'); // April 08, 2012

// get a predefined format
$date = Date::forge('last sunday')->format('datetime'); // 2012-04-08 00:00:00
$date = Date::forge('last sunday')->format('date'); // 2012-04-08
$date = Date::forge('last sunday')->format('time'); // 00:00:00

// amend the timestamp value, relative to existing value
$date = Date::forge('2012-04-05')->reforge('+ 3 days')->format('date'); // 2012-04-08

// get relative 'ago' format
$date = Date::forge('now - 10 minutes')->ago() // 10 minutes ago
```

## Math w/ Dates ##

Let's look at some date comparison examples:

```php
// passing objects
$date1 = Date::forge('2012-04-05');
$date2 = Date::forge('2012-04-08');
$diff = Date::diff($date1, $date2);
/*
DateInterval Object
(
    [y] => 0
    [m] => 0
    [d] => 3
    [h] => 0
    [i] => 0
    [s] => 0
    [invert] => 0
    [days] => 3
)
*/

// passing timestamps
$diff = Date::diff(1333598400, 1333857600);
/*
DateInterval Object
(
    [y] => 0
    [m] => 0
    [d] => 3
    [h] => 0
    [i] => 0
    [s] => 0
    [invert] => 0
    [days] => 3
)
*/

// passing strings
$diff = Date::diff('April 08, 2012', 'April 05, 2012');
/*
DateInterval Object
(
    [y] => 0
    [m] => 0
    [d] => 3
    [h] => 0
    [i] => 0
    [s] => 0
    [invert] => 1
    [days] => 3
)
*/
```

## Formatting ##

For help in building your formats, checkout the [PHP strftime() docs](http://php.net/manual/en/function.strftime.php).

## Notes ##

The class relies on ``strtotime()`` to make sense of your strings, and ``strftime()`` to make the format changes.  Just always check the ``time()`` output to see if you get false timestamps... which means the class couldn't understand what you were telling it.