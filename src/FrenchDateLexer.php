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
    public function __construct(array $dates = [], array $options = [])
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
     * @param int|string $number
     * @return string
     */
    public function ordinal($number): string
    {
        if ($number == 1) {
            return '1er';
        }
        return (string) $number;
    }


    /**
     * @param bool $onlyDay
     * @return string
     */
    public function toSentence($onlyDay = false): string
    {
        if (count($this->dates) > 0) {
            if ($this->isContinuous()) {
                if ($this->isSingleDay()) {
                    $sentence = $this->formatDay($this->getStartDate(), $onlyDay);
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
                /*
                 * Look for month groups
                 */
                foreach ($this->groupSpansByMonth() as $group) {
                    if ($group instanceof LexerInterface) {
                        $strings[] = $group->toSentence();
                    } elseif (is_array($group)) {
                        foreach ($group as $month => $monthSpans) {
                            $i = 0;
                            $determinant = count($monthSpans) > 1 ? 'les ' : 'le ';
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
