<?php

namespace AM\Date2Sentence;

use IntlDateFormatter;
use NumberFormatter;

class EnglishDateLexer extends AbstractDateLexer
{
    public function getLocale(): string
    {
        return 'en_US';
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
            'MMMM d'
        );
    }


    /**
     * @param int $number
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
        if (count($this->dates) > 0) {
            if ($this->isContinuous() && null !== $this->getStartDate()) {
                if (!$this->isSingleDay() && null !== $this->getEndDate()) {
                    $sentence = 'from ' . $this->formatDay($this->getStartDate()) . ' to ' . $this->formatDay($this->getEndDate());
                } else {
                    $sentence = $this->formatDay($this->getStartDate(), $onlyDay);
                }
            } else {
                $strings = [];

                /*
                 * Look for month groups
                 */
                foreach ($this->groupSpansByMonth() as $group) {
                    if ($group instanceof LexerInterface) {
                        $strings[] = $group->toSentence();
                    } elseif (\is_array($group)) {
                        foreach ($group as $monthSpans) {
                            $i = 0;
                            if ($monthSpans instanceof LexerInterface) {
                                $strings[] = $monthSpans->toSentence();
                            } elseif (\is_array($monthSpans)) {
                                foreach ($monthSpans as $monthSpan) {
                                    if ($monthSpan instanceof LexerInterface) {
                                        if ($i === 0) {
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
