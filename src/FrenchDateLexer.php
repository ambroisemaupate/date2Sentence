<?php

namespace AM\Date2Sentence;

use IntlDateFormatter;

class FrenchDateLexer extends AbstractDateLexer
{
    /**
     * @inheritDoc
     */
    public function getLocale(): string
    {
        return 'fr_FR';
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
            'd MMMM'
        );
    }

    /**
     * @param integer $number
     * @return string
     */
    public function ordinal($number): string
    {
        if ($number == 1) {
            return '1er';
        }
        return $number;
    }


    /**
     * @return string
     */
    public function toSentence(): string
    {
        if (count($this->dates) > 0) {
            if ($this->isContinuous()) {
                if ($this->isSingleDay()) {
                    $sentence = 'le ' . $this->formatDay($this->getStartDate());
                } else {
                    if ($this->isSameMonth()) {
                        // French can omit first month if same as end date
                        $sentence = 'du ' . $this->ordinal($this->getStartDate()->format('d')) . ' au ' . $this->formatDay($this->getEndDate());
                    } else {
                        $sentence = 'du ' . $this->formatDay($this->getStartDate()) . ' au ' . $this->formatDay($this->getEndDate());
                    }
                }
            } else {
                $strings = [];
                foreach ($this->getSubDateSpans() as $dateSpan) {
                    $strings[] = $dateSpan->toSentence();
                }

                $sentence = implode(', ', array_slice($strings, 0, -1));
                $sentence .= ' et ' . $strings[count($strings) - 1];
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
