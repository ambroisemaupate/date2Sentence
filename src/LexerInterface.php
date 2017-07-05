<?php

namespace AM\Date2Sentence;

use IntlDateFormatter;

interface LexerInterface
{
    public function toSentence(array $options = []);

    /**
     * @return IntlDateFormatter
     */
    public function getFormatter();

    /**
     * @param IntlDateFormatter $formatter
     * @return LexerInterface
     */
    public function setFormatter(IntlDateFormatter $formatter): LexerInterface;
}
