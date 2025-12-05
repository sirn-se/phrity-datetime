<?php

namespace Phrity\DateTime;

use DateInterval;
use DatePeriod;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use RangeException;
use Throwable;
use TypeError;

class Range
{
    private DateTimeImmutable $start;
    private DateTimeImmutable $end;

    public function __construct(DateTimeInterface|string $start, DateTimeInterface|DateInterval|string $end)
    {
        $this->start = $this->parseDateTime($start);
        $this->end = $this->parseDateTime($end, $this->start);
        if ($this->end < $this->start) {
            throw new RangeException("Invalid range.");
        }
        $this->end = $this->end->setTimezone($this->start->getTimezone());
    }

    public function getTimezone(): DateTimeZone
    {
        return $this->start->getTimezone();
    }

    public function setTimezone(DateTimeZone $timezone): self
    {
        return new self($this->start->setTimezone($timezone), $this->end->setTimezone($timezone));
    }

    public function getStart(): DateTimeImmutable
    {
        return $this->start;
    }

    public function getEnd(): DateTimeImmutable
    {
        return $this->end;
    }

    public function add(DateInterval $interval): self
    {
        return new self($this->start->add($interval), $this->end->add($interval));
    }

    public function sub(DateInterval $interval): self
    {
        return new self($this->start->sub($interval), $this->end->sub($interval));
    }

    public function modify(string $modifier): self
    {
        try {
            $start = @$this->start->modify($modifier);
            $end = @$this->end->modify($modifier);
            if (!$start || !$end) {
                throw new RangeException();
            }
        } catch (Throwable $e) {
            throw new RangeException('Invalid modifier.');
        }
        return new self($start, $end);
    }

    public function inRange(DateTimeInterface|Range|string $datetime): bool
    {
        if (is_string($datetime)) {
            $datetime = new DateTimeImmutable($datetime);
        }
        if ($datetime instanceof DateTimeInterface) {
            return $datetime >= $this->start && $datetime <= $this->end;
        }
        return $datetime->start >= $this->start && $datetime->end <= $this->end;
    }

    public function isBefore(DateTimeInterface|Range|string $datetime): bool
    {
        if (is_string($datetime)) {
            $datetime = new DateTimeImmutable($datetime);
        }
        if ($datetime instanceof DateTimeInterface) {
            return $datetime < $this->start;
        }
        return $datetime->start < $this->start;
    }

    public function isAfter(DateTimeInterface|Range|string $datetime): bool
    {
        if (is_string($datetime)) {
            $datetime = new DateTimeImmutable($datetime);
        }
        if ($datetime instanceof DateTimeInterface) {
            return $datetime > $this->end;
        }
        return $datetime->end > $this->end;
    }

    public function getInterval(): DateInterval
    {
        return $this->start->diff($this->end);
    }

    /**
     * @return DatePeriod<DateTimeImmutable, DateTimeImmutable, null>
     */
    public function getPeriod(DateInterval $interval, int $options = 0): DatePeriod
    {
        return new DatePeriod($this->start, $interval, $this->end, $options);
    }

    public function format(string $format): string
    {
        return "{$this->start->format($format)} - {$this->end->format($format)}";
    }

    public function __toString(): string
    {
        return $this->format('c');
    }

    private function parseDateTime(
        DateTimeInterface|DateInterval|string $input,
        DateTimeImmutable|null $relative = null
    ): DateTimeImmutable {
        if (is_string($input)) {
            return new DateTimeImmutable($input);
        }
        if ($input instanceof DateTimeImmutable) {
            return $input;
        }
        if ($input instanceof DateTime) {
            return DateTimeImmutable::createFromMutable($input);
        }
        if ($relative instanceof DateTimeImmutable) {
            return $relative->add($input);
        }
    }
}
