<?php

namespace AM\Date2Sentence;

use IntlDateFormatter;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractDateLexer implements LexerInterface
{
    /**
     * @var \DateTime[]
     */
    protected array $dates;

    /**
     * @var \DateTime[]
     */
    protected array $availableTimes;

    /**
     * @var array<int|string>
     */
    protected array $availableDaysOfWeek;

    /**
     * @var array<string, mixed>
     */
    protected array $options;
    protected bool $continuous = true;

    /**
     * @var LexerInterface[]
     */
    protected array $subDateSpans;
    protected bool $singleDay = true;
    protected bool $sameMonth = false;
    protected bool $sameYear = false;
    protected IntlDateFormatter $formatter;
    protected IntlDateFormatter $dayFormatter;
    protected IntlDateFormatter $monthFormatter;
    protected bool $subSpan = false;

    /**
     * Number of days of tolerance when computing continuity.
     *
     * A tolerance of 0 is: no tolerance.
     * A tolerance of 1 means there can be 1 day without a date.
     * etc
     *
     * @var int
     */
    protected int $tolerance = 0;

    /**
     * @param OptionsResolver $resolver
     * @return void
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'use_seconds' => false,
            'use_year' => false,
            'wrap_format' => '',
        ]);

        $resolver->setAllowedTypes('wrap_format', 'string');
        $resolver->setAllowedTypes('use_seconds', 'boolean');
        $resolver->setAllowedTypes('use_year', 'boolean');
    }

    /**
     * @param \DateTime[] $dates
     * @param array<string, mixed> $options
     */
    public function __construct(array $dates = [], array $options = [])
    {
        $this->dayFormatter = new IntlDateFormatter(
            $this->getLocale(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            \date_default_timezone_get(),
            IntlDateFormatter::GREGORIAN,
            'd'
        );

        $this->monthFormatter = new IntlDateFormatter(
            $this->getLocale(),
            IntlDateFormatter::NONE,
            IntlDateFormatter::NONE,
            \date_default_timezone_get(),
            IntlDateFormatter::GREGORIAN,
            'MMMM'
        );

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
        $this->setDates($dates);
    }

    /**
     * @return void
     */
    protected function sortDates()
    {
        usort($this->dates, function ($a, $b) {
            if ($a > $b) {
                return 1;
            } elseif ($a == $b) {
                return 0;
            } else {
                return -1;
            }
        });
    }

    protected function extractTimes(): void
    {
        foreach ($this->dates as $date) {
            if ($this->options['use_seconds'] === true) {
                $index = $date->format('H:i:s');
            } else {
                $index = $date->format('H:i');
            }
            if (!array_key_exists($index, $this->availableTimes)) {
                $this->availableTimes[$index] = new \DateTime('0000-00-00 ' . $index);
            }

            /*
             * Test if current span is single-day
             */
            if (
                null !== $this->getEndDate() &&
                null !== $this->getStartDate() &&
                $this->getEndDate()->format('Y-m-d') !== $this->getStartDate()->format('Y-m-d')
            ) {
                $this->singleDay = false;
            } else {
                $this->singleDay = true;
            }

            if (
                null !== $this->getEndDate() &&
                null !== $this->getStartDate() &&
                $this->getStartDate()->format('Y') === $this->getEndDate()->format('Y')
            ) {
                $this->sameYear = true;

                if ($this->getStartDate()->format('m') === $this->getEndDate()->format('m')) {
                    $this->sameMonth = true;
                }
            }
        }

        ksort($this->availableTimes);
    }

    protected function extractDaysOfWeek(): void
    {
        $this->availableDaysOfWeek = [];

        foreach ($this->dates as $date) {
            $index = $date->format('N');

            if (!in_array($index, $this->availableDaysOfWeek)) {
                $this->availableDaysOfWeek[] = $index;
            }
        }

        sort($this->availableDaysOfWeek);
    }

    /**
     * Test if date collection is continuous and
     * extract any sub date collections.
     *
     * @return void
     */
    protected function extractContinuity(): void
    {
        $this->continuous = true;
        $subSpanIndexes = [];

        /*
         * Extract continuous indexes
         */
        $firstIndex = 0;
        $previous = $this->dates[0];
        for ($i = 1; $i < count($this->dates); $i++) {
            $diff = $this->dates[$i]->diff($previous)->days;

            if ($diff > ($this->getTolerance() + 1)) {
                $this->continuous = false;
                $subSpanIndexes[] = [$firstIndex, $i - 1];
                $firstIndex = $i;
            }
            $previous = $this->dates[$i];
        }
        /*
         * Close last span if not continuous
         */
        if ($this->continuous === false) {
            $subSpanIndexes[] = [$firstIndex, count($this->dates) - 1];
        }

        /*
         * If main span is not continuous:
         * create sub spans.
         */
        if (count($subSpanIndexes) > 0) {
            foreach ($subSpanIndexes as $subSpanIndex) {
                $dates = [];
                list($firstIndex, $lastIndex) = $subSpanIndex;
                for ($i = $firstIndex; $i <= $lastIndex; $i++) {
                    $dates[] = $this->dates[$i];
                }
                $this->createSubSpan($dates);
            }
        }
    }

    /**
     * Group sub-spans by months.
     *
     * @return array<int, LexerInterface|array<LexerInterface|array<LexerInterface>>>
     */
    protected function groupSpansByMonth(): array
    {
        $spans = [];
        if (count($this->getSubDateSpans()) > 0) {
            $lastDaysGroup = [];
            $lastDaysMonth = null;
            foreach ($this->getSubDateSpans() as $dateSpan) {
                if ($dateSpan->isSingleDay() && null !== $dateSpan->getStartDate()) {
                    // Get a month identifier
                    $month = $dateSpan->getStartDate()->format('Y-m');
                    /*
                     * Change month, need to start a new group
                     */
                    if (null !== $lastDaysMonth && $lastDaysMonth !== $month) {
                        $spans[] = [$month => $lastDaysGroup];
                        $lastDaysGroup = [];
                    }
                    /*
                     * Add day to current month group
                     */
                    $lastDaysGroup[] = $dateSpan;
                    $lastDaysMonth = $month;
                } else {
                    /*
                     * Add previous month group if existing
                     */
                    if (null !== $lastDaysMonth) {
                        $spans[] = [$lastDaysMonth => $lastDaysGroup];
                    }
                    /*
                     * Simply add continuous span as a group
                     */
                    $spans[] = $dateSpan;
                    $lastDaysGroup = [];
                    $lastDaysMonth = null;
                }
            }
            /*
             * Add last month group if existing
             */
            if (null !== $lastDaysMonth) {
                $spans[] = [$lastDaysMonth => $lastDaysGroup];
            }
        }
        return $spans;
    }

    /**
     * @param \DateTime[] $dates
     * @return $this
     */
    protected function createSubSpan(array $dates): self
    {
        $subSpan = new static([], $this->options);
        $subSpan->setTolerance($this->getTolerance());
        $subSpan->setDates($dates);
        $subSpan->setSubSpan(true);
        $this->subDateSpans[] = $subSpan;

        return $this;
    }

    /**
     * @param \DateTime $dateTime
     * @param string $format [Y-m-d]
     * @return bool
     */
    public function dateExists(\DateTime $dateTime, string $format = 'Y-m-d'): bool
    {
        $formattedDate = $dateTime->format($format);
        foreach ($this->dates as $date) {
            if ($formattedDate === $date->format($format)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int|string $number
     * @return string
     */
    public function ordinal($number): string
    {
        return (string) $number;
    }

    /**
     * @return bool
     */
    public function isContinuous(): bool
    {
        return $this->continuous;
    }

    /**
     * @return \DateTime|null
     */
    public function getStartDate(): ?\DateTime
    {
        if (count($this->dates) > 0) {
            return $this->dates[0];
        }
        return null;
    }

    /**
     * @return \DateTime|null
     */
    public function getEndDate(): ?\DateTime
    {
        if (count($this->dates) > 0) {
            return $this->dates[count($this->dates) - 1];
        }
        return null;
    }

    /**
     * @return LexerInterface[]
     */
    public function getSubDateSpans(): array
    {
        return $this->subDateSpans;
    }

    /**
     * @return bool
     */
    public function isSingleDay(): bool
    {
        return $this->singleDay;
    }

    /**
     * @return bool
     */
    public function isSameMonth(): bool
    {
        return $this->sameMonth;
    }

    /**
     * @return bool
     */
    public function isSameYear(): bool
    {
        return $this->sameYear;
    }

    /**
     * @inheritDoc
     */
    public function getFormatter(): IntlDateFormatter
    {
        return $this->formatter;
    }

    /**
     * @inheritDoc
     */
    public function setFormatter(IntlDateFormatter $formatter): LexerInterface
    {
        $this->formatter = $formatter;
        return $this;
    }

    /**
     * @return IntlDateFormatter
     */
    public function getDayFormatter(): IntlDateFormatter
    {
        return $this->dayFormatter;
    }

    /**
     * @param IntlDateFormatter $dayFormatter
     * @return LexerInterface
     */
    public function setDayFormatter(IntlDateFormatter $dayFormatter): LexerInterface
    {
        $this->dayFormatter = $dayFormatter;
        return $this;
    }

    /**
     * @return IntlDateFormatter
     */
    public function getMonthFormatter(): IntlDateFormatter
    {
        return $this->monthFormatter;
    }

    /**
     * @param IntlDateFormatter $monthFormatter
     * @return LexerInterface
     */
    public function setMonthFormatter(IntlDateFormatter $monthFormatter): LexerInterface
    {
        $this->monthFormatter = $monthFormatter;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSubSpan(): bool
    {
        return $this->subSpan;
    }

    /**
     * @param bool $subSpan
     * @return AbstractDateLexer
     */
    public function setSubSpan(bool $subSpan): AbstractDateLexer
    {
        $this->subSpan = $subSpan;
        return $this;
    }

    /**
     * @param \DateTime $date
     * @param bool $onlyDay
     * @return string
     */
    protected function formatDay(\DateTime $date, bool $onlyDay = false): string
    {
        if ($onlyDay) {
            $formatted = $this->getDayFormatter()->format($date);
        } else {
            $formatted = $this->getFormatter()->format($date);
        }

        if (false === $formatted) {
            throw new \InvalidArgumentException('Date cannot be formatted');
        }

        /*
         * Add ordinals
         */
        $numbers = [];
        if (false !== preg_match('#([0-9]{1,2})#', $formatted, $numbers)) {
            if (count($numbers) > 1) {
                $formatted = preg_replace('#([0-9]{1,2})#', $this->ordinal($numbers[1]), $formatted);
            }
        }

        /*
         * Wrap if necessary
         */
        if (!empty($this->options['wrap_format'])) {
            return sprintf($this->options['wrap_format'], $formatted);
        }

        if (null === $formatted) {
            throw new \InvalidArgumentException('Date cannot be formatted');
        }

        return $formatted;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if (!$this->isContinuous()) {
            $strings = [];
            foreach ($this->getSubDateSpans() as $dateSpan) {
                $strings[] = $dateSpan->__toString();
            }

            return implode(', ', $strings);
        } elseif (null !== $this->getStartDate()) {
            if (!$this->isSingleDay() && null !== $this->getEndDate()) {
                return $this->getStartDate()->format('Y-m-d') . ' --> ' . $this->getEndDate()->format('Y-m-d');
            }
            return $this->getStartDate()->format('Y-m-d');
        }

        return '';
    }

    /**
     * @{inheritdoc}
     */
    public function getAvailableTimes(): array
    {
        return $this->availableTimes;
    }

    /**
     * @return int
     */
    public function getTolerance(): int
    {
        return $this->tolerance;
    }

    /**
     * @param int $tolerance
     * @return AbstractDateLexer
     */
    public function setTolerance(int $tolerance): self
    {
        $this->tolerance = $tolerance;
        return $this;
    }

    /**
     * @return \DateTime[]
     */
    public function getDates(): array
    {
        return $this->dates;
    }

    /**
     * @param array<\DateTime|null> $dates
     * @return AbstractDateLexer
     */
    public function setDates(array $dates): self
    {
        $this->continuous = true;
        $this->singleDay = true;
        $this->sameMonth = false;
        $this->sameYear = false;
        $this->subSpan = false;
        $this->subDateSpans = [];
        $this->availableTimes = [];
        $this->dates = $dates;

        foreach ($this->dates as $date) {
            if (!($date instanceof \DateTime)) {
                throw new \InvalidArgumentException('All dates must be instances of \DateTime.');
            }
        }

        if (count($this->dates) > 0) {
            $this->sortDates();
            $this->extractContinuity();
            $this->extractTimes();
            $this->extractDaysOfWeek();
        }

        return $this;
    }

    public function getAvailableDaysOfWeek(): array
    {
        return $this->availableDaysOfWeek;
    }

    /**
     * @return array<\DateTime|array<\DateTime|array<\DateTime>>>
     */
    public function toArray(): array
    {
        if (count($this->dates) > 0) {
            if ($this->isContinuous() && null !== $this->getStartDate()) {
                if (!$this->isSingleDay() && null !== $this->getEndDate()) {
                    return [$this->getStartDate(), $this->getEndDate()];
                }
                return [$this->getStartDate()];
            } else {
                $spans = [];
                foreach ($this->getSubDateSpans() as $dateSpan) {
                    $spans[] = $dateSpan->toArray();
                }
                return $spans;
            }
        }
        return [];
    }
}
