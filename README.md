PHP Interview Test
========

Quick setup and testing
-------

**Setup**

- Install php, composer and phpunit on local

    `brew install php`
    
    `brew install composer`
    
    `brew install phpunit`
    
    `composer install`

**Testing**    
- Manual test with video output 

    `php bin/diff.php 2015/12/25 2016/12/25`
    
- Launch phpunit test: 

    `vendor/bin/phpunit`


Assumptions
-----------

Calculate the difference between two given dates without using any of the built in PHP date functions or objects.


* Dates will be provided in the format “YYYY/MM/DD” for example “2015/03/21”.
* All dates will be based on the Gregorian calendar.
* The return value should be an object in the format:

```php
stdClass {
    int   $years,        // The number of years between the two dates.
    int   $months,       // The number of months between the two dates.
    int   $days,         // The number of days between the two dates less the months and the years.
    int   $total_days,   // The total days between the two dates including the months and years.
    bool  $invert        // true if the the difference is negative (i.e. $start > $end).
}
```

* You are free to structure the rest of your code however you like.
* Basic unit tests are provided however you are free to add tests to cover anything additional you feel is required.


Requirements
------------
* >= PHP 7.2: http://php.net
* >= Composer: https://getcomposer.org
* >= PHPUnit 7.0: https://phpunit.de/getting-started.html