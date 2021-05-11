PSALM := vendor/bin/psalm
PHPUNIT := vendor/bin/phpunit

psalm-analyse:
	$(PSALM) src

run-all-tests:
	$(PHPUNIT) tests

run-static-tests:
	$(PHPUNIT) tests/Static

run-runtime-tests:
	$(PHPUNIT) tests/Runtime
