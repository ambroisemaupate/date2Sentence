# Date2Sentence
A simple lexer to print human readable dates.

[![Build Status](https://travis-ci.org/ambroisemaupate/date2Sentence.svg?branch=master)](https://travis-ci.org/ambroisemaupate/date2Sentence)

### Requires: 

- PHP 7.0 min.
- PHP-intl extension

### Usage

```bash
composer require ambroisemaupate/date-to-sentence
```

```php
use AM\Date2Sentence\EnglishDateLexer;

$lexer = new EnglishDateLexer();

$lexer->setDates([
     new DateTime('2017-06-01'),
     new DateTime('2017-06-02'),
     new DateTime('2017-06-03'),
 ]);

echo $lexer->toSentence();
// "From June 1st to June 3rd"


$lexer->setDates([
     new DateTime('2017-06-01'),
     new DateTime('2017-06-02'),
     new DateTime('2017-06-03'),
     new DateTime('2017-06-10'),
 ]);

echo $lexer->toSentence();
// "From June 1st to June 3rd and June 10th"
```
#### With wrap option

```php
use AM\Date2Sentence\EnglishDateLexer;

$nonContinuousLexer = new EnglishDateLexer([
    new DateTime('2017-06-01'),
    new DateTime('2017-06-02'),
    new DateTime('2017-06-03'),
    new DateTime('2017-06-10'),
], ['wrap_format' => '<span>%s</span>']);

echo $nonContinuousLexer->toSentence();
// "From <span>June 1st</span> to <span>June 3rd</span> and <span>June 10th</span>"
```

#### With French lexer

```php
use AM\Date2Sentence\FrenchDateLexer;

$lexer = new FrenchDateLexer([
    new DateTime('2017-06-01'),
    new DateTime('2017-06-02'),
    new DateTime('2017-06-03'),
]);

echo $lexer->toSentence();
// "Du 1er au 3 juin"

$nonContinuousLexer = new FrenchDateLexer([
    new DateTime('2017-06-01'),
    new DateTime('2017-06-02'),
    new DateTime('2017-06-03'),
    new DateTime('2017-06-10'),
]);

echo $nonContinuousLexer->toSentence();
// "Du 1er au 3 juin et le 10 juin"
```

#### Setting tolerance

Set tolerance **before** dates to changes how days are listed. You can force
*from-to* writing even if there are some missing days (e.g. weekends).

```php
use AM\Date2Sentence\EnglishDateLexer;

$nonContinuousLexer = new EnglishDateLexer();
// Tolerate 1 missing day between dates.
$nonContinuousLexer->setTolerance(1);
$nonContinuousLexer->setDates([
    new DateTime('2017-06-01'),
    // no 2nd
    new DateTime('2017-06-03'),
    // no 4th
    new DateTime('2017-06-05'),
    // no 6th
    new DateTime('2017-06-07'),
]);

echo $nonContinuousLexer->toSentence();
// "From June 1st to June 7th"
```

#### Grouping by month

*Date2Sentence* is made to group days within the same month not 
to repeat same month-name over and over.

```php
use AM\Date2Sentence\EnglishDateLexer;

$nonContinuousLexer = new EnglishDateLexer();
$nonContinuousLexer->setDates([
    new DateTime('2017-06-01'),
    // no 2nd
    new DateTime('2017-06-03'),
    // no 4th
    new DateTime('2017-06-05'),
    // no 6th
    new DateTime('2017-06-07'),
    new DateTime('2017-07-01'),
]);

echo $nonContinuousLexer->toSentence();
// "June 1st, 3rd, 5th, 7th and July 1st"

//
// In French, it works too…
// "Les 1er, 2, 5, 7 juin et le 1er juillet"
```

#### Get hours

*Date2Sentence* can also extract times from your given dates:

```php
use AM\Date2Sentence\EnglishDateLexer;

$lexer = new EnglishDateLexer();
$lexer->setDates([
    new DateTime('2017-06-01 20:00:00'),
    new DateTime('2017-06-03 21:00:00'),
    new DateTime('2017-06-05 20:00:00'),
    new DateTime('2017-06-07 21:00:00'),
    new DateTime('2017-07-01 20:00:00'),
]);

echo json_encode(array_keys($this->getLexer()->getAvailableTimes()));
// [
//    "20:00",
//    "21:00"
// ]
```

#### Get days of week

*Date2Sentence* can also extract days of week from your given dates, day will be
represented as their number (1 for Monday, 7 for Sunday) and will be ordered:

```php
use AM\Date2Sentence\EnglishDateLexer;

$lexer = new EnglishDateLexer();
$lexer->setDates([
    new DateTime('2017-06-01'),
    new DateTime('2017-06-02'),
    new DateTime('2017-06-03'),
    new DateTime('2017-06-08'),
    new DateTime('2017-06-08'),
]);

echo json_encode($this->getLexer()->getAvailableDaysOfWeek());
// [
//    4,
//    5,
//    6
// ]
```

## Tests

```bash
vendor/phpunit/phpunit/phpunit --bootstrap vendor/autoload.php test
```
