.PHONY: test
test:
	vendor/phpunit/phpunit/phpunit --bootstrap vendor/autoload.php test
	vendor/bin/phpcs --report=full -p
	vendor/bin/phpstan analyse -c phpstan.neon
