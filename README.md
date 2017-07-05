# Date2Sentence
A simple lexer to print human readable dates.

### Requires: 

- PHP 7.0 min.
- PHP-intl extension

### Usage

```php
use AM\Date2Sentence\EnglishDateLexer;

$lexer = new EnglishDateLexer([
    new DateTime('2017-06-01'),
    new DateTime('2017-06-02'),
    new DateTime('2017-06-03'),
]);

echo $lexer->toSentence();
// "From June 1 to June 3"


$nonContinuouslexer = new EnglishDateLexer([
    new DateTime('2017-06-01'),
    new DateTime('2017-06-02'),
    new DateTime('2017-06-03'),
    new DateTime('2017-06-10'),
]);

echo $nonContinuouslexer->toSentence();
// "From June 1 to June 3 and June 10"
```
