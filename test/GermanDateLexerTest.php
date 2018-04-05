<?php

use AM\Date2Sentence\GermanDateLexer;
use PHPUnit\Framework\TestCase;

class GermanDateLexerTest extends TestCase
{
    /**
     * @dataProvider toSentenceProvider
     * @param $dates
     * @param $expected
     */
    public function testToSentence($dates, $expected)
    {
        $lexer = new GermanDateLexer();
        $lexer->setDates($dates);
        $this->assertEquals($expected, $lexer->toSentence());
    }

    /**
     * @return array
     */
    public function toSentenceProvider(): array
    {
        return [
            [
                [],
                ''
            ],
            [
                [
                    new DateTime('2017-11-25 17:00:00'),
                    new DateTime('2017-11-26 15:00:00'),
                ],
                '25. bis 26. November'
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
                '1. bis 15. Juni'
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
                '15. Juni bis 2. Juli'
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
                '15. bis 19. Juni, 21. Juni und 23. Juni bis 2. Juli'
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
                '15. bis 21. Juni und 23. Juni bis 2. Juli'
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
                '21. Juni und 23. Juni bis 2. Juli'
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
                '21., 23., 25., 27., 29. Juni und 1. Juli'
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
                '21., 23., 25., 27., 29. Juni, 1. Juli, 21., 23., 25., 27., 29. September und 1. Oktober',
            ]
        ];
    }
}
