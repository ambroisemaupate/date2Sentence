<?php

namespace AM\Date2Sentence;


use IntlDateFormatter;

class EnglishDateLexer extends AbstractDateLexer
{
    /**
     * @inheritDoc
     */
    public function __construct($dates, array $options = [])
    {
        parent::__construct($dates, $options);

        /**
         * Set a default EN formatter
         */
        $this->formatter = IntlDateFormatter::create(
            'en_GB',
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
    protected function ordinal($number): string {
        $ends = array('th','st','nd','rd','th','th','th','th','th','th');
        if ((($number % 100) >= 11) && (($number%100) <= 13)) {
            return $number. 'th';
        } else {
            return $number. $ends[$number % 10];
        }
    }

    /**
     * @param array $options
     * @return string
     */
    public function toSentence(array $options = [])
    {
        if ($this->isContinuous()) {
            if ($this->isSingleDay()) {
                $sentence = $this->getFormatter()->format($this->getStartDate());
            } else {
                $sentence = 'from ' . $this->getFormatter()->format($this->getStartDate()) . ' to ' . $this->getFormatter()->format($this->getEndDate());
            }
        } else {
            $strings = [];
            foreach ($this->getSubDateSpans() as $dateSpan) {
                $strings[] = $dateSpan->toSentence($options);
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
}
