<?php

use AM\Date2Sentence\FrenchDateLexer;
use PHPUnit\Framework\TestCase;

class FrenchDateLexerTest extends TestCase
{
    /**
     * @dataProvider toSentenceProvider
     * @param $dates
     * @param $expected
     */
    public function testToSentence($dates, $expected)
    {
        $lexer = new FrenchDateLexer($dates);
        $this->assertEquals($expected, $lexer->toSentence());
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
                'Du 1er au 15 juin'
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
                'Du 15 juin au 2 juillet'
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
                'Du 15 au 19 juin, le 21 juin et du 23 juin au 2 juillet'
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
                'Du 15 au 21 juin et du 23 juin au 2 juillet'
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
                'Le 21 juin et du 23 juin au 2 juillet'
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
                'Les 21, 23, 25, 27, 29 juin et le 1er juillet'
            ]
        ];
    }
}
