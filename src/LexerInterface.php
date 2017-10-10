<?php

namespace AM\Date2Sentence;

use IntlDateFormatter;

interface LexerInterface
{
    /**
     * @return string
     */
    public function getLocale(): string;

    /**
     * @param integer $number
     * @return string
     */
    public function ordinal($number): string;

    /**
     * @param bool $onlyDay
     * @return string
     */
    public function toSentence($onlyDay = false): string;

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
    public function getDates();

    /**
     * @param \DateTime[] $dates
     * @return LexerInterface
     */
    public function setDates(array $dates);

    /**
     * ISO-8601 numeric representation of the day of the week.
     *
     * 1 (for Monday) through 7 (for Sunday)
     *
     * @return int[]
     */
    public function getAvailableDaysOfWeek();

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
    public function getStartDate();

    /**
     * @return \DateTime|null
     */
    public function getEndDate();

    /**
     * @return bool
     */
    public function isContinuous(): bool;

    /**
     * @param \DateTime $dateTime
     * @param string $format [Y-m-d]
     * @return bool
     */
    public function dateExists(\DateTime $dateTime, $format = 'Y-m-d');

    /**
     * @return array
     */
    public function toArray();
}
