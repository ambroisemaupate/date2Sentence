<?php

namespace AM\Date2Sentence;


use IntlDateFormatter;
use NumberFormatter;

class EnglishDateLexer extends AbstractDateLexer
{
    /**
     * @inheritDoc
     */
    public function getLocale(): string
    {
        return 'en_US';
    }

    /**
     * @inheritDoc
     */
    public function __construct($dates = [], array $options = [])
    {
        parent::__construct($dates, $options);

        /**
         * Set a default EN formatter
         */
        $this->formatter = IntlDateFormatter::create(
            $this->getLocale(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            \date_default_timezone_get(),
            IntlDateFormatter::GREGORIAN,
            'MMMM d'
        );
    }


    /**
     * @param integer $number
     * @return string
     */
    public function ordinal($number): string
    {
        $formatter = new NumberFormatter($this->getLocale(), NumberFormatter::ORDINAL);
        return $formatter->format($number);
    }

    /**
     * @return string
     */
    public function toSentence(): string
    {
        if (count($this->dates) > 0) {
            if ($this->isContinuous()) {
                if ($this->isSingleDay()) {
                    $sentence = $this->formatDay($this->getStartDate());
                } else {
                    $sentence = 'from ' . $this->formatDay($this->getStartDate()) . ' to ' . $this->formatDay($this->getEndDate());
                }
            } else {
                $strings = [];
                foreach ($this->getSubDateSpans() as $dateSpan) {
                    $strings[] = $dateSpan->toSentence();
                }

                $sentence = implode(', ', array_slice($strings, 0, -1));
                $sentence .= ' and ' . $strings[count($strings) - 1];
            }

            if ($this->isSubSpan()) {
                return $sentence;
            } else {
                return ucfirst($sentence);
            }
        }
        return '';
    }
}
