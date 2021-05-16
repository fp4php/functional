PHP=php
PSALM=vendor/bin/psalm
PHPUNIT=vendor/bin/phpunit

build-doc:
	php ./linker.php

psalm-analyse:
	$(PSALM) src

run-all-tests:
	$(PHPUNIT) tests

run-static-tests:
	$(PHPUNIT) tests/Static

run-runtime-tests:
	$(PHPUNIT) tests/Runtime

