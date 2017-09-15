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
     * @return IntlDateFormatter
     */
    public function getDayFormatter(): IntlDateFormatter;

    /**
     * @return IntlDateFormatter
     */
    public function getMonthFormatter(): IntlDateFormatter;

    /**
     * @param IntlDateFormatter $formatter
     * @return LexerInterface
     */
    public function setFormatter(IntlDateFormatter $formatter): LexerInterface;

    /**
     * @return \DateTime[]
     */
    public function getDates();

    /**
     * @param \DateTime[] $dates
     * @return LexerInterface
     */
    public function setDates(array $dates);
}
