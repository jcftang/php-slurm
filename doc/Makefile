PHPDOC:=$(shell \
	if phpdoc -h > /dev/null ; then \
		echo phpdoc; \
		touch .docs-available; \
	else \
		echo "Warning: phpdoc not installed, can't generate html output." >&2; \
		echo "@echo Skipping: phpdoc"; \
		rm .docs-available; \
	fi)

default: php

php:
	$(PHPDOC) --output HTML:frames:phpedit \
	--directory  ../examples \
	--packageoutput c,includes,objects \
	--target php \
	--sourcecode \
	--title "php-slurm"

clean:
	-rm -rf php .docs-available

.PHONY: php
