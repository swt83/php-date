# Date for LaravelPHP #

Working w/ dates can be a bear.  While this helper class probably is missing some things, I find it helpful.

## Examples ##

Working examples should make it pretty clear how easy this is to use:

```php
// default timestamp is now
$date = Date::forge();

// set a custom timestamp
$date = Date::forge('last sunday');

// get a nice format
$date = Date::forge('last sunday')->format('%B %d, %Y'); // April 15, 2012

// get a predefined format
$date = Date::forge('last sunday')->format('datetime'); // 2012-04-15 00:00:00
$date = Date::forge('last sunday')->format('date'); // 2012-04-15
$date = Date::forge('last sunday')->format('datetime'); // 00:00:00

// amend the timestamp value, relative to existing value
$date = Date::forge('2012-04-12')->format('date'); // 2012-04-12
$date->reforge('+ 3 days')->format('date'); // 2012-04-15
```

Let's look at some date comparison examples:

```php
// passing objects
$date1 = Date::forge('2012-04-12');
$date2 = Date::forge('2012-04-15');
$diff = Date::compare($date1, $date2);

/*
$diff = Array
(
    [years] => 0
    [months] => 0
    [days] => 3
    [hours] => 0
    [minutes] => 0
    [seconds] => 0
    [invert] => 0
)
*/

// passing strings
$diff = Date::compare('April 15, 2012', 'April 12, 2012');

/*
$diff = Array
(
    [years] => 0
    [months] => 0
    [days] => 3
    [hours] => 0
    [minutes] => 0
    [seconds] => 0
    [invert] => 1
)
*/
```

## Formatting ##

For help in building your formats, checkout the [PHP ``strftime()`` docs](http://php.net/manual/en/function.strftime.php).

## Notes ##

The class relies on ``strtotime()`` to make sense of your strings, and ``strftime()`` to make the format changes.  Just always check the ``time()`` output to see if you get false timestamps... which means the class couldn't understand what you were telling it.