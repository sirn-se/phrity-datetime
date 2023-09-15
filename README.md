[![Build Status](https://github.com/sirn-se/phrity-datetime/actions/workflows/acceptance.yml/badge.svg)](https://github.com/sirn-se/phrity-datetime/actions)
[![Coverage Status](https://coveralls.io/repos/github/sirn-se/phrity-datetime/badge.svg?branch=main)](https://coveralls.io/github/sirn-se/phrity-datetime?branch=main)

# Introduction

DateTime utilities.

## Installation

Install with [Composer](https://getcomposer.org/);
```
composer require phrity/datetime
```

## Usage

Create a range, using two DateTime instances or a DateTime and a DateInterval for relative end date.
```php
use Phrity\DateTime\Range;

$start = new DateTime("2023-05-17 13:23");
$end = new DateTime("2023-06-20 23:23");
$now = new DateTime();
$interval = new DateInterval("P15D");
$timezone = new DateTimeZone("+02:00");

$range_1 = new Range($start, $end);
echo "$range_1"; // "2023-05-17T13:23:00+00:00 - 2023-06-20T23:23:00+00:00"
$range_2 = new Range($start, $interval);
echo "$range_2"; // "2023-05-17T13:23:00+00:00 - 2023-06-01T13:23:00+00:00"
```

Basic operations
```php
$range_1->getTimezone(); // -> DateTimeZone
$range_1->setTimezone($timezone);

$range_1->getStart(); // -> DateTimeImmutable
$range_1->getEnd(); // -> DateTimeImmutable

$range_1->format("c"); // Format using the same options as for DateTime
```

Modifiers
```php
$range_1->add($interval); // Adds interval on start/end of range
$range_1->sub($interval); // Subtracts interval on start/end of range
$range_1->modify("+1 month"); // Modifies start/end of range
```

Checkers
```php
$range_1->inRange($now); // If now is within range
$range_1->isBefore($now); // If now is before range
$range_1->isAfter($now); // If now is after range

$range_1->inRange($range_2); // If range_2 is within range_1
$range_1->isBefore($range_2); // If range_2 starts before range_1
$range_1->isAfter($range_2); // If range_2 ends after range_1
```

Intervals and periods
```php
$range_1->getInterval(); // -> DateInterval, between start and end
$range_1->getPeriod($interval); // -> DatePeriod, intervals between start and end
```

## Versions

| Version | PHP | |
| --- | --- | --- |
| `1.0` | `^7.4\|^8.0` | DateTime Range class |
