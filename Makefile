
VENDOR=./vendor/bin/
PHPUNIT=php -dxdebug.enabled=1 -dxdebug.mode=coverage ./vendor/bin/phpunit --coverage-text
.PHONY: bootstrap setup update server test fmt cs lint stan report clean reset

bootstrap:
	composer install

setup: bootstrap

update:
	composer upgrade

server:
	php -S localhost:5555 -t public/

test:
	rm -f public/tests && ln -s ../tests public/tests
	{ php -S 127.0.0.1:8081 -t public/ >& /dev/null & }; \
	PID=$$!; \
	$(PHPUNIT); \
	RES=$$?; \
	kill $$PID; \
	rm public/tests
	exit $$RES

clean:
	rm -fr ./cache/*
	rm -fr ./custom/config/*
	rm -fr ./custom/config.* ./custom/people.*

reset: clean
	rm -fr vendor
	rm composer.lock

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
		app,custom,docs,tests,public \
		html \
		cleancode,codesize,controversial,design,naming,unusedcode > tmp/report.html

serve:
	php -S localhost:5555 -t public/

clean:
	rm -fr ./cache/*
	rm -fr ./custom/config/*
	rm -fr ./custom/config.* ./custom/people.*
