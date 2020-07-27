PHP_VERSION := 7.2
php := docker run --rm -v $(PWD):/app -w /app php:$(PHP_VERSION) php
phpunit := docker run --rm -v $(PWD):/app -w /app php:$(PHP_VERSION) vendor/bin/phpunit
kahlan := docker run --rm -v $(PWD):/app -w /app php:$(PHP_VERSION) vendor/bin/kahlan
phpstan := docker run --rm -v $(PWD):/app -w /app php:$(PHP_VERSION) vendor/bin/phpstan
composer := docker run --rm -v $(PWD):/app -v $(HOME)/.composer:/tmp -w /app composer:latest composer


setup:
	$(composer) install --prefer-dist

validate:
	$(composer) validate

composer-require: OPT :=
composer-require: P :=
composer-require:
	$(composer) require $(OPT) $(P)
composer-remove: P :=
composer-remove:
	$(composer) remove $(P)

.PHONY: test lint
test: lint
	$(phpunit) -c phpunit.xml
	$(kahlan) --spec=spec

lint:
	$(phpstan) analyse -c phpstan.neon
