<?php

namespace AM\Date2Sentence;

use IntlDateFormatter;
use NumberFormatter;

class GermanDateLexer extends AbstractDateLexer
{
    public function getLocale(): string
    {
        return 'de_DE';
    }

    public function __construct(array $dates = [], array $options = [])
    {
        parent::__construct($dates, $options);

        /**
         * Set a default EN formatter
         */
        $this->formatter = new IntlDateFormatter(
            $this->getLocale(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            \date_default_timezone_get(),
            IntlDateFormatter::GREGORIAN,
            'd MMMM'
        );
    }


    /**
     * @param int|float $number
     * @return string
     */
    public function ordinal($number): string
    {
        $formatter = new NumberFormatter($this->getLocale(), NumberFormatter::ORDINAL);
        $string = $formatter->format($number);
        if (!\is_string($string)) {
            throw new \RuntimeException('Could not format ordinal');
        }
        return $string;
    }

    /**
     * @param bool $onlyDay
     * @return string
     */
    public function toSentence(bool $onlyDay = false): string
    {
        $sentence = '';

        if (count($this->dates) > 0) {
            if ($this->isContinuous() && null !== $this->getStartDate()) {
                if ($this->isSingleDay()) {
                    $sentence = $this->formatDay($this->getStartDate(), $onlyDay);
                } elseif (null !== $this->getEndDate()) {
                    if ($this->isSameMonth()) {
                        // German can omit first month if same as end date
                        $sentence = $this->ordinal((int) $this->getStartDate()->format('d')) . ' bis ' . $this->formatDay($this->getEndDate());
                    } else {
                        $sentence = $this->formatDay($this->getStartDate()) . ' bis ' . $this->formatDay($this->getEndDate());
                    }
                }
            } else {
                $strings = [];

                /*
                 * Look for month groups
                 */
                foreach ($this->groupSpansByMonth() as $group) {
                    if ($group instanceof LexerInterface) {
                        $strings[] = $group->toSentence();
                    } elseif (is_array($group)) {
                        foreach ($group as $monthSpans) {
                            if ($monthSpans instanceof LexerInterface) {
                                $strings[] = $monthSpans->toSentence();
                            } else {
                                $i = 0;
                                $determinant = '';
                                foreach ($monthSpans as $monthSpan) {
                                    if ($monthSpan instanceof LexerInterface) {
                                        if ($i === 0 && $i === count($monthSpans) - 1) {
                                            $strings[] = $determinant . $monthSpan->toSentence(false);
                                        } elseif ($i === 0) {
                                            $strings[] = $determinant . $monthSpan->toSentence(true);
                                        } elseif ($i === count($monthSpans) - 1) {
                                            $strings[] = $monthSpan->toSentence(false);
                                        } else {
                                            $strings[] = $monthSpan->toSentence(true);
                                        }
                                        $i++;
                                    }
                                }
                            }
                        }
                    }
                }

                $sentence = implode(', ', array_slice($strings, 0, -1));
                $sentence .= ' und ' . $strings[count($strings) - 1];
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
