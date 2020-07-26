PHP_VERSION := 7.2
php := docker run --rm -v $(PWD):/app -w /app php:$(PHP_VERSION) php
phpunit := docker run --rm -v $(PWD):/app -w /app php:$(PHP_VERSION) vendor/bin/phpunit
kahlan := docker run --rm -v $(PWD):/app -w /app php:$(PHP_VERSION) vendor/bin/kahlan
composer := docker run --rm -v $(PWD):/app -w /app composer:latest composer


setup:
	$(composer) install --prefer-dist

composer-require: OPT :=
composer-require: P :=
composer-require:
	$(composer) require $(OPT) $(P)
composer-remove: P :=
composer-remove:
	$(composer) remove $(P)
composer-%:
	$(composer) $*

test:
	$(phpunit) -c phpunit.xml
	$(kahlan) --spec=spec
