PHP_VERSION := 7.2
php := docker run --rm -v $(PWD):/app -w /app php:$(PHP_VERSION) php
phpunit := docker run --rm -v $(PWD):/app -w /app php:$(PHP_VERSION) vendor/bin/phpunit
kahlan := docker run --rm -v $(PWD):/app -w /app php:$(PHP_VERSION) vendor/bin/kahlan
phpstan := docker run --rm -v $(PWD):/app -w /app php:$(PHP_VERSION) vendor/bin/phpstan
phpcsfixer := docker run --rm -v $(PWD):/app -w /app php:$(PHP_VERSION) vendor/bin/php-cs-fixer
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

.PHONY: test
test: lint format-dry
	$(phpunit) -c phpunit.xml
	$(kahlan) --spec=spec

.PHONY: lint
lint:
	$(phpstan) analyse -c phpstan.neon

.PHONY: format format-dry
format:
	$(phpcsfixer) fix --rules=@PSR2 ./

format-dry:
	$(phpcsfixer) fix --rules=@PSR2 --dry-run --diff ./
