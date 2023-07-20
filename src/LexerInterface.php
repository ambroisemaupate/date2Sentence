<?php

namespace AM\Date2Sentence;

use IntlDateFormatter;

interface LexerInterface
{
    /**
     * @param array<\DateTime> $dates
     * @param array<string, mixed> $options
     */
    public function __construct(array $dates = [], array $options = []);

    /**
     * @return string
     */
    public function getLocale(): string;

    /**
     * @param int|string $number
     * @return string
     */
    public function ordinal($number): string;

    /**
     * @param bool $onlyDay
     * @return string
     */
    public function toSentence(bool $onlyDay = false): string;

    /**
     * @return IntlDateFormatter
     */
    public function getFormatter(): IntlDateFormatter;

    /**
     * @param IntlDateFormatter $formatter
     * @return LexerInterface
     */
    public function setFormatter(IntlDateFormatter $formatter): LexerInterface;

    /**
     * @return IntlDateFormatter
     */
    public function getDayFormatter(): IntlDateFormatter;

    /**
     * @param IntlDateFormatter $dayFormatter
     * @return LexerInterface
     */
    public function setDayFormatter(IntlDateFormatter $dayFormatter): LexerInterface;

    /**
     * @return IntlDateFormatter
     */
    public function getMonthFormatter(): IntlDateFormatter;

    /**
     * @param IntlDateFormatter $monthFormatter
     * @return LexerInterface
     */
    public function setMonthFormatter(IntlDateFormatter $monthFormatter): LexerInterface;

    /**
     * @return \DateTime[]
     */
    public function getDates(): array;

    /**
     * @param \DateTime[] $dates
     * @return LexerInterface
     */
    public function setDates(array $dates): self;

    /**
     * ISO-8601 numeric representation of the day of the week.
     *
     * 1 (for Monday) through 7 (for Sunday)
     *
     * @return array<int|string>
     */
    public function getAvailableDaysOfWeek(): array;

    /**
     * @return \DateTime[]
     */
    public function getAvailableTimes(): array;

    /**
     * @return bool
     */
    public function isSingleDay(): bool;

    /**
     * @return \DateTime|null
     */
    public function getStartDate(): ?\DateTime;

    /**
     * @return \DateTime|null
     */
    public function getEndDate(): ?\DateTime;

    /**
     * @return bool
     */
    public function isContinuous(): bool;

    /**
     * @param \DateTime $dateTime
     * @param string $format [Y-m-d]
     * @return bool
     */
    public function dateExists(\DateTime $dateTime, string $format = 'Y-m-d'): bool;

    /**
     * @return array<int, \DateTime|array<\DateTime>>
     */
    public function toArray(): array;

    /**
     * @return string
     */
    public function __toString(): string;
}
