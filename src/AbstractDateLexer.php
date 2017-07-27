<?php

namespace AM\Date2Sentence;

use IntlDateFormatter;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractDateLexer implements LexerInterface
{
    /**
     * @var array|\DateTime[]
     */
    protected $dates;

    /**
     * @var \DateTime[]
     */
    protected $availableTimes;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var boolean
     */
    protected $continuous;

    /**
     * @var LexerInterface[]
     */
    protected $subDateSpans;

    /**
     * @var boolean
     */
    protected $singleDay;

    /**
     * @var boolean
     */
    protected $sameMonth = false;

    /**
     * @var boolean
     */
    protected $sameYear = false;

    /**
     * @var IntlDateFormatter
     */
    protected $formatter;

    /**
     * @var bool
     */
    protected $subSpan = false;

    /**
     * @param OptionsResolver $resolver
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
     * AbstractDateLexer constructor.
     * @param \DateTime[] $dates
     * @param array $options
     */
    public function __construct(array $dates, array $options = [])
    {
        $this->dates = $dates;
        $this->subDateSpans = [];

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);

        foreach ($this->dates as $date) {
            if ($date === null) {
                throw new \InvalidArgumentException('One date cannot be null.');
            }
            if (!($date instanceof \DateTime)) {
                throw new \InvalidArgumentException('All dates must be instances of \DateTime.');
            }
        }

        $this->sortDates();
        $this->extractContinuity();

        if ($this->isContinuous()) {
            $this->extractTimes();
        }
    }


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

    protected function extractTimes()
    {
        /** @var \DateTime $date */
        foreach ($this->dates as $date) {
            if ($this->options['use_seconds'] === true) {
                $index = $date->format('H:i:s');
            } else {
                $index = $date->format('H:i');
            }
            if (!isset($this->availableTimes[$index])) {
                $this->availableTimes[$index] = new \DateTime('0000-00-00 ' . $index);
            }

            /*
             * Test if current span is single-day
             */
            if ($this->getEndDate()->diff($this->getStartDate())->days >= 1) {
                $this->singleDay = false;
            } else {
                $this->singleDay = true;
            }

            if ($this->getStartDate()->format('Y') === $this->getEndDate()->format('Y')) {
                $this->sameYear = true;

                if ($this->getStartDate()->format('m') === $this->getEndDate()->format('m')) {
                    $this->sameMonth = true;
                }
            }
        }
    }

    /**
     * Test if date collection is continuous and
     * extract any sub date collections.
     */
    protected function extractContinuity()
    {
        $this->continuous = true;
        $subSpanIndexes = [];

        /*
         * Extract continuous indexes
         */
        $firstIndex = 0;
        $previous = $this->dates[0];
        for ($i = 1; $i < count($this->dates); $i++) {
            if ($this->dates[$i]->diff($previous)->days > 1) {
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
         * create subspans.
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
     * @param \DateTime[] $dates
     * @return $this
     */
    protected function createSubSpan(array $dates)
    {
        $subSpan = new static($dates, $this->options);
        $subSpan->setSubSpan(true);
        $this->subDateSpans[] = $subSpan;

        return $this;
    }

    /**
     * @param \DateTime $dateTime
     * @param string $format [Y-m-d]
     * @return bool
     */
    public function dateExists(\DateTime $dateTime, $format = 'Y-m-d')
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
     * @param integer $number
     * @return string
     */
    public function ordinal($number): string
    {
        return $number;
    }

    /**
     * @return bool
     */
    public function isContinuous(): bool
    {
        return $this->continuous;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate(): \DateTime
    {
        return $this->dates[0];
    }

    /**
     * @return \DateTime
     */
    public function getEndDate(): \DateTime
    {
        return $this->dates[count($this->dates) - 1];
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
     * @return string
     */
    protected function formatDay(\DateTime $date): string
    {
        $formatted = $this->getFormatter()->format($date);

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
        
        return $formatted;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if (!$this->isContinuous()) {
            $strings = [];
            foreach ($this->getSubDateSpans() as $dateSpan) {
                $strings[] = (string) $dateSpan;
            }

            return implode(', ', $strings);
        } else {
            if ($this->isSingleDay()) {
                return $this->getStartDate()->format('Y-m-d');
            }
            return $this->getStartDate()->format('Y-m-d') . ' --> ' . $this->getEndDate()->format('Y-m-d');
        }
    }

    /**
     * @return \DateTime[]
     */
    public function getAvailableTimes(): array
    {
        return $this->availableTimes;
    }
}
