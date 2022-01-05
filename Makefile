

VENDOR=./vendor/bin/


test:
	{ php -S 127.0.0.1:8081 >& /dev/null & }; \
	    PID=$$!; \
        $(VENDOR)phpunit; \
        RES=$$?; \
        kill $$PID; \
        exit $$RES

fmt:
	$(VENDOR)phpcbf

cs:
	$(VENDOR)phpcs

lint:
	$(VENDOR)parallel-lint --exclude vendor .

report:
	$(VENDOR)phpmd \
		admin,app,custom,docs,tests,atom.php,cron.php,index.php,install.php,postload.php \
		html \
		cleancode,codesize,controversial,design,naming,unusedcode > report.html

serve:
	php -S localhost:5555 -t .
