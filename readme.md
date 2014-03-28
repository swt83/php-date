# Date

A PHP library for working w/ dates.

The ``ago()`` and ``draw_calendar()`` methods are Laravel specific.

## Install

Normal install via Composer.

### Provider

Register the service provider in your ``app/config/app.php`` file:

```php
'Travis\Date\Provider'
```

## Examples

Some basic examples:

```php
// default timestamp is now
$date = Travis\Date::forge();

// pass timestamps
$date = Travis\Date::forge(1333857600);

// pass strings
$date = Travis\Date::forge('last sunday');

// pass objects
$date = Travis\Date::forge('last sunday');
$new_date = Travis\Date::forge($date);

// get timestamp
$date = Travis\Date::forge('last sunday')->time(); // 1333857600

// get a nice format
$date = Travis\Date::forge('last sunday')->format('%B %d, %Y'); // April 08, 2012

// amend the timestamp value, relative to existing value
$date = Travis\Date::forge('2012-04-05')->reforge('+ 3 days')->format('%F'); // 2012-04-08

// amend the timestamp value, and keep original date object
$date = Travis\Date::forge('2012-04-05');
$new_date = $date->reforge('+3 days', true); // flag to return modified cloned object
echo $date->format('%F'); // 2013-04-05
echo $new_date->format('%F'); // 2014-04-08

// get relative 'ago' format
$date = Travis\Date::forge('now - 10 minutes')->ago() // 10 minutes ago
```

## Math w/ Dates

Let's look at some date comparison examples:

```php
// passing objects
$date1 = Travis\Date::forge('2012-04-05');
$date2 = Travis\Date::forge('2012-04-08');
$diff = Travis\Date::diff($date1, $date2);
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
$diff = Travis\Date::diff(1333598400, 1333857600);
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
$diff = Travis\Date::diff('April 08, 2012', 'April 05, 2012');
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

## Drawing Calendars

You can print a nice HTML table of a calendar:

```php
$html = Travis\Date::draw_calendar($month, $year); // both params should be integers
```

You can print custom content inside the cells for a specific date by passing a closure:

```php
$html = Travis\Date::draw_calendar($month, $year, function($date) use ($my_custom_param) {
    if ($date->format('%F') == $my_custom_param)
    {
        echo 'something special';
    }
});
```

Take a look at the view used in the package and you'll see what CSS options are available.

## Notes

For help in building your formats, checkout the [PHP strftime() docs](http://php.net/manual/en/function.strftime.php).