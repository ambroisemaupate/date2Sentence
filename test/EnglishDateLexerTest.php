<?php

use AM\Date2Sentence\EnglishDateLexer;
use PHPUnit\Framework\TestCase;

class EnglishDateLexerTest extends TestCase
{
    /**
     * @dataProvider availableTimesProvider
     * @param $dates
     * @param $expected
     */
    public function testGetAvailableTimes($dates, $expected)
    {
        $lexer = new EnglishDateLexer();
        $lexer->setDates($dates);
        $this->assertEquals($expected, $lexer->getAvailableTimes());
    }

    /**
     * @dataProvider availableDaysOfWeekProvider
     * @param $dates
     * @param $expected
     */
    public function testGetAvailableDaysOfWeek($dates, $expected)
    {
        $lexer = new EnglishDateLexer();
        $lexer->setDates($dates);
        $this->assertEquals($expected, $lexer->getAvailableDaysOfWeek());
    }

    /**
     * @dataProvider toSentenceProvider
     * @param $dates
     * @param $expected
     */
    public function testToSentence($dates, $expected)
    {
        $lexer = new EnglishDateLexer();
        $lexer->setDates($dates);
        $this->assertEquals($expected, $lexer->toSentence());
    }

    /**
     * @dataProvider toSentenceProvider
     * @param $dates
     * @param $expectedNoTolerance
     * @param $expectedWithTolerance
     */
    public function testToSentenceWithTolerance($dates, $expectedNoTolerance, $expectedWithTolerance)
    {
        $lexer = new EnglishDateLexer();
        $lexer->setTolerance(2);
        $lexer->setDates($dates);
        $this->assertEquals($expectedWithTolerance, $lexer->toSentence());
    }

    /**
     * @dataProvider isContinuousProvider
     * @param $dates
     * @param $expected
     */
    public function testIsContinuous($dates, $expected)
    {
        $lexer = new EnglishDateLexer();
        $lexer->setDates($dates);
        $this->assertEquals($expected, $lexer->isContinuous());

        if (!$lexer->isContinuous()) {
            $this->assertNotCount(0, $lexer->getSubDateSpans());
        }
    }

    /**
     * @dataProvider isContinuousProvider
     * @param $dates
     * @param $isContinuous
     * @param $spanCount
     */
    public function testHasSubDateSpans($dates, $isContinuous, $spanCount)
    {
        $lexer = new EnglishDateLexer();
        $lexer->setDates($dates);
        $this->assertCount($spanCount, $lexer->getSubDateSpans());
    }

    /**
     * @return array
     */
    public function availableTimesProvider(): array
    {
        return [
            [
                [
                    new DateTime("2017-06-30 19:12:05"),
                    new DateTime("2017-06-30 19:12:05"),
                    new DateTime("2017-06-30 19:12:05"),
                    new DateTime("2017-07-27 15:32:00")
                ],
                [
                    '15:32' => new DateTime('0000-00-00 15:32:00'),
                    '19:12' => new DateTime('0000-00-00 19:12:00')
                ]
            ],
            [
                [
                    new DateTime('2017-06-01 00:00:00'),
                    new DateTime('2017-06-02 00:00:00'),
                    new DateTime('2017-06-03 00:00:00'),
                    new DateTime('2017-06-04 00:00:00'),
                    new DateTime('2017-06-05 00:00:00'),
                    new DateTime('2017-06-06 00:00:00'),
                ],
                [
                    '00:00' => new DateTime('0000-00-00 00:00:00')
                ]
            ],
            [
                [
                    new DateTime('2017-06-01 01:30:00'),
                    new DateTime('2017-06-02 01:30:00'),
                    new DateTime('2017-06-02 02:00:00'),
                    new DateTime('2017-06-03 02:00:00'),
                    new DateTime('2017-06-03 03:00:00'),
                    new DateTime('2017-06-04 03:00:00'),
                ],
                [
                    '01:30' => new DateTime('0000-00-00 01:30:00'),
                    '02:00' => new DateTime('0000-00-00 02:00:00'),
                    '03:00' => new DateTime('0000-00-00 03:00:00')
                ]
            ],
            [
                [
                    new DateTime('2017-06-01 01:00:00'),
                    new DateTime('2017-06-02 01:00:00'),
                    new DateTime('2017-06-03 01:00:00'),
                    new DateTime('2017-06-04 01:00:00'),
                ],
                [
                    '01:00' => new DateTime('0000-00-00 01:00:00')
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function availableDaysOfWeekProvider(): array
    {
        return [
            [
                [
                    new DateTime("2017-06-30 19:12:05"),
                    new DateTime("2017-06-30 19:12:05"),
                    new DateTime("2017-06-30 19:12:05"),
                    new DateTime("2017-07-27 15:32:00")
                ],
                [
                    4,
                    5,
                ]
            ],
            [
                [
                    new DateTime('2017-06-01 00:00:00'),
                    new DateTime('2017-06-02 00:00:00'),
                    new DateTime('2017-06-03 00:00:00'),
                    new DateTime('2017-06-04 00:00:00'),
                    new DateTime('2017-06-05 00:00:00'),
                    new DateTime('2017-06-06 00:00:00'),
                ],
                [
                    1,
                    2,
                    4,
                    5,
                    6,
                    7,
                ]
            ],
            [
                [
                    new DateTime('2017-06-01 01:00:00'),
                    new DateTime('2017-06-08 02:00:00'),
                    new DateTime('2017-06-15 01:00:00'),
                    new DateTime('2017-06-22 01:00:00'),
                ],
                [
                    4
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function isContinuousProvider(): array
    {
        return [
            [[
                new DateTime('2017-06-01'),
                new DateTime('2017-06-02'),
                new DateTime('2017-06-03'),
                new DateTime('2017-06-04'),
                new DateTime('2017-06-05'),
                new DateTime('2017-06-06'),
                new DateTime('2017-06-07'),
                new DateTime('2017-06-08'),
                new DateTime('2017-06-09'),
                new DateTime('2017-06-10'),
                new DateTime('2017-06-11'),
                new DateTime('2017-06-12'),
                new DateTime('2017-06-13'),
                new DateTime('2017-06-14'),
                new DateTime('2017-06-15'),
            ], true, 0],
            [[
                new DateTime('2017-06-01'),
                new DateTime('2017-06-02'),
                new DateTime('2017-06-03'),
                new DateTime('2017-06-04'),
                new DateTime('2017-06-05'),
                //
                new DateTime('2017-06-07'),
                //
                new DateTime('2017-06-09'),
                new DateTime('2017-06-10'),
                new DateTime('2017-06-11'),
                //
                new DateTime('2017-06-13'),
                new DateTime('2017-06-14'),
                new DateTime('2017-06-15'),
            ], false, 4],
            [[
                new DateTime('2017-06-01 00:00:00'),
                new DateTime('2017-06-02 00:00:00'),
                new DateTime('2017-06-03 00:00:00'),
                new DateTime('2017-06-04 00:00:00'),
                new DateTime('2017-06-05 00:00:00'),
                new DateTime('2017-06-05 00:00:00'),
                new DateTime('2017-06-05 01:00:00'),
                new DateTime('2017-06-05 12:00:00'),
                new DateTime('2017-06-11 00:00:00'),
                new DateTime('2017-06-06 00:00:00'),
                new DateTime('2017-06-12 00:00:00'),
                new DateTime('2017-06-07 00:00:00'),
                new DateTime('2017-06-09 00:00:00'),
                new DateTime('2017-06-10 00:00:00'),
                new DateTime('2017-06-13 00:00:00'),
                new DateTime('2017-06-08 00:00:00'),
                new DateTime('2017-06-14 00:00:00'),
                new DateTime('2017-06-15 00:00:00'),
            ], true, 0]
        ];
    }

    /**
     * @return array
     */
    public function toSentenceProvider(): array
    {
        return [
            [
                [],
                '',
                ''
            ],
            [
                [
                    new DateTime('2017-06-01'),
                    new DateTime('2017-06-02'),
                    new DateTime('2017-06-03'),
                    new DateTime('2017-06-04'),
                    new DateTime('2017-06-05'),
                    new DateTime('2017-06-06'),
                    new DateTime('2017-06-07'),
                    new DateTime('2017-06-08'),
                    new DateTime('2017-06-09'),
                    new DateTime('2017-06-10'),
                    new DateTime('2017-06-11'),
                    new DateTime('2017-06-12'),
                    new DateTime('2017-06-13'),
                    new DateTime('2017-06-14'),
                    new DateTime('2017-06-15'),
                ],
                'From June 1st to June 15th',
                'From June 1st to June 15th'
            ],
            [
                [
                    new DateTime('2017-06-15'),
                    new DateTime('2017-06-16'),
                    new DateTime('2017-06-17'),
                    new DateTime('2017-06-18'),
                    new DateTime('2017-06-19'),
                    new DateTime('2017-06-20'),
                    new DateTime('2017-06-21'),
                    new DateTime('2017-06-22'),
                    new DateTime('2017-06-23'),
                    new DateTime('2017-06-24'),
                    new DateTime('2017-06-25'),
                    new DateTime('2017-06-26'),
                    new DateTime('2017-06-27'),
                    new DateTime('2017-06-28'),
                    new DateTime('2017-06-29'),
                    new DateTime('2017-06-30'),
                    new DateTime('2017-07-01'),
                    new DateTime('2017-07-02'),
                ],
                'From June 15th to July 2nd',
                'From June 15th to July 2nd'
            ],
            [
                [
                    new DateTime('2017-06-15'),
                    new DateTime('2017-06-16'),
                    new DateTime('2017-06-17'),
                    new DateTime('2017-06-18'),
                    new DateTime('2017-06-19'),
                    //
                    new DateTime('2017-06-21'),
                    //
                    new DateTime('2017-06-23'),
                    new DateTime('2017-06-24'),
                    new DateTime('2017-06-25'),
                    new DateTime('2017-06-26'),
                    new DateTime('2017-06-27'),
                    new DateTime('2017-06-28'),
                    new DateTime('2017-06-29'),
                    new DateTime('2017-06-30'),
                    new DateTime('2017-07-01'),
                    new DateTime('2017-07-02'),
                ],
                'From June 15th to June 19th, June 21st and from June 23rd to July 2nd',
                'From June 15th to July 2nd'
            ],
            [
                [
                    new DateTime('2017-06-15'),
                    new DateTime('2017-06-16'),
                    new DateTime('2017-06-17'),
                    new DateTime('2017-06-18'),
                    new DateTime('2017-06-19'),
                    new DateTime('2017-06-20'),
                    new DateTime('2017-06-21'),
                    //
                    new DateTime('2017-06-23'),
                    new DateTime('2017-06-24'),
                    new DateTime('2017-06-25'),
                    new DateTime('2017-06-26'),
                    new DateTime('2017-06-27'),
                    new DateTime('2017-06-28'),
                    new DateTime('2017-06-29'),
                    new DateTime('2017-06-30'),
                    new DateTime('2017-07-01'),
                    new DateTime('2017-07-02'),
                ],
                'From June 15th to June 21st and from June 23rd to July 2nd',
                'From June 15th to July 2nd'
            ],
            [
                [
                    new DateTime('2017-06-21'),
                    //
                    new DateTime('2017-06-23'),
                    new DateTime('2017-06-24'),
                    new DateTime('2017-06-25'),
                    new DateTime('2017-06-26'),
                    new DateTime('2017-06-27'),
                    new DateTime('2017-06-28'),
                    new DateTime('2017-06-29'),
                    new DateTime('2017-06-30'),
                    new DateTime('2017-07-01'),
                    new DateTime('2017-07-02'),
                ],
                'June 21st and from June 23rd to July 2nd',
                'From June 21st to July 2nd'
            ],
            [
                [
                    new DateTime('2017-06-21'),
                    //
                    new DateTime('2017-06-23'),
                    //
                    new DateTime('2017-06-25'),
                    //
                    new DateTime('2017-06-27'),
                    //
                    new DateTime('2017-06-29'),
                    //
                    new DateTime('2017-07-01'),
                ],
                'June 21st, 23rd, 25th, 27th, 29th and July 1st',
                'From June 21st to July 1st'
            ],
            [
                [
                    new DateTime('2017-06-21'),
                    //
                    new DateTime('2017-06-23'),
                    //
                    new DateTime('2017-06-25'),
                    //
                    new DateTime('2017-06-27'),
                    //
                    new DateTime('2017-06-29'),
                    //
                    new DateTime('2017-07-01'),
                    //////
                    new DateTime('2017-09-21'),
                    //
                    new DateTime('2017-09-23'),
                    //
                    new DateTime('2017-09-25'),
                    //
                    new DateTime('2017-09-27'),
                    //
                    new DateTime('2017-09-29'),
                    //
                    new DateTime('2017-10-01'),
                ],
                'June 21st, 23rd, 25th, 27th, 29th, July 1st, September 21st, 23rd, 25th, 27th, 29th and October 1st',
                'From June 21st to July 1st and from September 21st to October 1st'
            ]
        ];
    }
}
