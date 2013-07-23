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

// amend the timestamp value, relative to existing value
$date = Date::forge('2012-04-05')->reforge('+ 3 days')->format('%F'); // 2012-04-08

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

## Drawing Calendars ##

You can print a nice HTML table of a calendar:

```php
$html = Date::draw_calendar($month, $year); // both params should be integers
```

You can print custom content inside the cells for a specific date by passing a closure:

```php
$html = Date::draw_calendar($month, $year, function($date) use ($my_custom_param) {
    if ($date->format('%F') == $my_custom_param)
    {
        echo 'something special';
    }
});
```

Take a look at the view used in the package and you'll see what CSS options are available.

## Notes ##

For help in building your formats, checkout the [PHP strftime() docs](http://php.net/manual/en/function.strftime.php).