<?php

declare(strict_types=1);

namespace Phrity\DateTime;

use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Exception;
use PHPUnit\Framework\TestCase;
use RangeException;
use TypeError;

class DateTimeTest extends TestCase
{
    public function testDateTimeImmutableInput()
    {
        $start = new DateTimeImmutable('2023-09-15 13:28:55+00:00');
        $end = new DateTimeImmutable('2023-09-15 13:28:55-01:00');
        $range = new Range($start, $end);
        $this->assertInstanceOf(DateTimeImmutable::class, $range->getStart());
        $this->assertInstanceOf(DateTimeImmutable::class, $range->getEnd());
        $this->assertEquals($start, $range->getStart());
        $this->assertEquals($end, $range->getEnd());
    }

    public function testDateTimeInput()
    {
        $start = new DateTime('2023-09-15 13:28:55+00:00');
        $end = new DateTime('2023-09-15 13:28:55-01:00');
        $range = new Range($start, $end);
        $this->assertInstanceOf(DateTimeImmutable::class, $range->getStart());
        $this->assertInstanceOf(DateTimeImmutable::class, $range->getEnd());
        $this->assertEquals($start, $range->getStart());
        $this->assertEquals($end, $range->getEnd());
    }

    public function testStringInput()
    {
        $start = new DateTime('2023-09-15 13:28:55+00:00');
        $end = new DateTime('2023-09-15 13:28:55-01:00');
        $range = new Range('2023-09-15 13:28:55+00:00', '2023-09-15 13:28:55-01:00');
        $this->assertInstanceOf(DateTimeImmutable::class, $range->getStart());
        $this->assertInstanceOf(DateTimeImmutable::class, $range->getEnd());
        $this->assertEquals($start, $range->getStart());
        $this->assertEquals($end, $range->getEnd());
    }

    public function testDateIntervalInput()
    {
        $start = new DateTimeImmutable('2023-09-15 13:28:55+00:00');
        $end = new DateTime('2023-09-15 13:28:55-01:00');
        $interval = new DateInterval('PT1H');
        $range = new Range($start, $interval);
        $this->assertInstanceOf(DateTimeImmutable::class, $range->getStart());
        $this->assertInstanceOf(DateTimeImmutable::class, $range->getEnd());
        $this->assertEquals($start, $range->getStart());
        $this->assertEquals($end, $range->getEnd());
    }

    public function testTimezone()
    {
        $start = new DateTimeImmutable('2023-09-15 13:28:55+00:00');
        $end = new DateTimeImmutable('2023-09-15 13:28:55-01:00');
        $range = new Range($start, $end);
        $this->assertInstanceOf(DateTimeZone::class, $range->getTimezone());
        $this->assertEquals('+00:00', $range->getTimezone()->getName());
        $changed = $range->setTimezone(new DateTimeZone('-02:00'));
        $this->assertInstanceOf(DateTimeZone::class, $changed->getTimezone());
        $this->assertEquals('-02:00', $changed->getTimezone()->getName());
    }

    public function testFormat()
    {
        $start = new DateTimeImmutable('2023-09-15 13:28:55+00:00');
        $end = new DateTimeImmutable('2023-09-15 13:28:55-01:00');
        $range = new Range($start, $end);
        $this->assertEquals('2023-09-15T13:28:55+00:00 - 2023-09-15T14:28:55+00:00', $range->format('c'));
        $this->assertEquals('2023-09-15T13:28:55+00:00 - 2023-09-15T14:28:55+00:00', "{$range}");
    }

    public function testModifiers()
    {
        $start = new DateTimeImmutable('2023-09-15 13:28:55+00:00');
        $end = new DateTimeImmutable('2023-09-15 14:28:55+00:00');
        $range = new Range($start, $end);
        $add = $range->add(new DateInterval('PT1H'));
        $this->assertNotSame($range, $add);
        $this->assertEquals(new DateTimeImmutable('2023-09-15 14:28:55+00:00'), $add->getStart());
        $this->assertEquals(new DateTimeImmutable('2023-09-15 15:28:55+00:00'), $add->getEnd());
        $sub = $range->sub(new DateInterval('PT1H'));
        $this->assertNotSame($range, $sub);
        $this->assertEquals(new DateTimeImmutable('2023-09-15 12:28:55+00:00'), $sub->getStart());
        $this->assertEquals(new DateTimeImmutable('2023-09-15 13:28:55+00:00'), $sub->getEnd());
        $mod = $range->modify('-1 hour');
        $this->assertNotSame($range, $mod);
        $this->assertEquals(new DateTimeImmutable('2023-09-15 12:28:55+00:00'), $mod->getStart());
        $this->assertEquals(new DateTimeImmutable('2023-09-15 13:28:55+00:00'), $mod->getEnd());
    }

    public function testRangesDateTime()
    {
        $start = new DateTimeImmutable('2023-09-15 13:28:55+00:00');
        $end = new DateTimeImmutable('2023-09-15 13:28:55-01:00');
        $before = new DateTimeImmutable('2023-09-15 13:28:54+00:00');
        $after = new DateTimeImmutable('2023-09-15 13:28:56-01:00');
        $range = new Range($start, $end);
        $this->assertTrue($range->inRange($start));
        $this->assertTrue($range->inRange($end));
        $this->assertFalse($range->inRange($before));
        $this->assertFalse($range->inRange($after));
        $this->assertFalse($range->isBefore($start));
        $this->assertFalse($range->isBefore($end));
        $this->assertTrue($range->isBefore($before));
        $this->assertFalse($range->isBefore($after));
        $this->assertFalse($range->isAfter($start));
        $this->assertFalse($range->isAfter($end));
        $this->assertFalse($range->isAfter($before));
        $this->assertTrue($range->isAfter($after));
    }

    public function testRangesRange()
    {
        $range = new Range('2023-09-15 13:28:55+00:00', '2023-09-15 13:28:55-01:00');
        $within = new Range('2023-09-15 13:28:56+00:00', '2023-09-15 13:28:54-01:00');
        $before = new Range('2023-09-15 13:28:53+00:00', '2023-09-15 13:28:54+00:00');
        $after = new Range('2023-09-15 13:28:56-01:00', '2023-09-15 13:28:57-01:00');
        $before_overlap = new Range('2023-09-15 13:28:54+00:00', '2023-09-15 13:28:55-01:00');
        $after_overlap = new Range('2023-09-15 13:28:55+00:00', '2023-09-15 13:28:56-01:00');
        $this->assertTrue($range->inRange($range));
        $this->assertTrue($range->inRange($within));
        $this->assertFalse($range->inRange($before));
        $this->assertFalse($range->inRange($after));
        $this->assertFalse($range->inRange($before_overlap));
        $this->assertFalse($range->inRange($after_overlap));
        $this->assertFalse($range->isBefore($range));
        $this->assertFalse($range->isBefore($within));
        $this->assertTrue($range->isBefore($before));
        $this->assertFalse($range->isBefore($after));
        $this->assertTrue($range->isBefore($before_overlap));
        $this->assertFalse($range->isBefore($after_overlap));
        $this->assertFalse($range->isAfter($range));
        $this->assertFalse($range->isAfter($within));
        $this->assertFalse($range->isAfter($before));
        $this->assertTrue($range->isAfter($after));
        $this->assertFalse($range->isAfter($before_overlap));
        $this->assertTrue($range->isAfter($after_overlap));
    }

    public function testIntervals()
    {
        $start = new DateTimeImmutable('2023-09-15 13:28:55+00:00');
        $end = new DateTimeImmutable('2023-09-15 13:28:55-01:00');
        $range = new Range($start, $end);
        $interval = $range->getInterval();
        $this->assertInstanceOf(DateInterval::class, $interval);
        $this->assertEquals('01', $interval->format('%H'));

        $period = $range->getPeriod(new DateInterval('PT1M'));
        $this->assertInstanceOf(DatePeriod::class, $period);
        $this->assertEquals($start, $period->getStartDate());
        $this->assertEquals($end, $period->getEndDate());
    }

    public function testStartTypeError()
    {
        $start = null;
        $end = new DateTimeImmutable('2023-09-15 13:28:55-01:00');
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('Phrity\DateTime\Range::__construct(): Argument #1 ($start) must be of type DateTimeInterface or string.');
        $range = new Range($start, $end);
    }

    public function testStartParseError()
    {
        $start = '23-23-23';
        $end = new DateTimeImmutable('2023-09-15 13:28:55-01:00');
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to parse time string (23-23-23) at position 0');
        $range = new Range($start, $end);
    }

    public function testEndTypeError()
    {
        $start = new DateTimeImmutable('2023-09-15 13:28:55+00:00');
        $end = null;
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('Phrity\DateTime\Range::__construct(): Argument #2 ($end) must be of type DateTimeInterface, DateInterval or string.');
        $range = new Range($start, $end);
    }

    public function testEndParseError()
    {
        $start = new DateTimeImmutable('2023-09-15 13:28:55+00:00');
        $end = '23-23-23';
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to parse time string (23-23-23) at position 0');
        $range = new Range($start, $end);
    }

    public function testRangeError()
    {
        $end = new DateTimeImmutable('2023-09-15 13:28:55+00:00');
        $start = new DateTimeImmutable('2023-09-15 13:28:55-01:00');
        $this->expectException(RangeException::class);
        $this->expectExceptionMessage('Phrity\DateTime\Range::__construct(): Argument #2 ($end) be same or later than Argument #1 ($start).');
        $range = new Range($start, $end);
    }
}