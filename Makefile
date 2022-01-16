
VENDOR=./vendor/bin/
PHPUNIT=php -dxdebug.enabled=1 -dxdebug.mode=coverage ./vendor/bin/phpunit --coverage-text

test:
	{ php -S 127.0.0.1:8081 >& /dev/null & }; \
	PID=$$!; \
	$(PHPUNIT); \
	RES=$$?; \
	kill $$PID; \
	exit $$RES

fmt:
	$(VENDOR)phpcbf

cs:
	$(VENDOR)phpcs

lint:
	$(VENDOR)parallel-lint --exclude vendor .

stan:
	$(VENDOR)phpstan analyze -c phpstan.neon

report:
	$(VENDOR)phpmd \
		admin,app,custom,docs,tests,atom.php,cron.php,index.php,install.php,postload.php \
		html \
		cleancode,codesize,controversial,design,naming,unusedcode > tmp/report.html

serve:
	php -S localhost:5555 -t .
