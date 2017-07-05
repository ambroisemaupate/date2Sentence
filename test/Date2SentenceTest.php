<?php

use AM\Date2Sentence\EnglishDateLexer;
use PHPUnit\Framework\TestCase;

class Date2SentenceTest extends TestCase
{
    /**
     * @dataProvider toSentenceProvider
     */
    public function testToSentence($dates, $expected)
    {
        $lexer = new EnglishDateLexer($dates);

        $this->assertEquals($expected, $lexer->toSentence([
            'day' => true,
            'month' => true,
            'year' => false,
            'hour' => false,
            'minute' => false,
            'second' => false,
        ]));
    }

    /**
     * @dataProvider isContinuousProvider
     */
    public function testIsContinuous($dates, $expected)
    {
        $lexer = new EnglishDateLexer($dates);
        $this->assertEquals($expected, $lexer->isContinuous());

        if (!$lexer->isContinuous()) {
            $this->assertNotCount(0, $lexer->getSubDateSpans());
        }
    }

    /**
     * @dataProvider isContinuousProvider
     */
    public function testHasSubDateSpans($dates, $isContinuous, $spanCount)
    {
        $lexer = new EnglishDateLexer($dates);
        $this->assertCount($spanCount, $lexer->getSubDateSpans());
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
                'From June 1 to June 15'
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
                'From June 15 to July 2'
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
                'From June 15 to June 19, June 21 and from June 23 to July 2'
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
                'From June 15 to June 21 and from June 23 to July 2'
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
                'June 21 and from June 23 to July 2'
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
                'June 21, June 23, June 25, June 27, June 29 and July 1'
            ]
        ];
    }
}
