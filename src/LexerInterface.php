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
     * @return string
     */
    public function toSentence(): string;

    /**
     * @return IntlDateFormatter
     */
    public function getFormatter(): IntlDateFormatter;

    /**
     * @param IntlDateFormatter $formatter
     * @return LexerInterface
     */
    public function setFormatter(IntlDateFormatter $formatter): LexerInterface;
}
