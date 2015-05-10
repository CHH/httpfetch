.PHONY: test

test:
	php -S localhost:8003 -t tests/web 2>&1 > /dev/null &
	phpunit
	pkill php -S localhost:8003 -t tests/web
