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

## Tests

```bash
vendor/phpunit/phpunit/phpunit --bootstrap vendor/autoload.php test
```
