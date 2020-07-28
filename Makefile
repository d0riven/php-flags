PHP_VERSION := 7.2

PHP        := docker run --rm -v $(PWD):/app -w /app php:$(PHP_VERSION)
PHPUNIT    := $(PHP) vendor/bin/phpunit
KAHLAN     := $(PHP) vendor/bin/kahlan
PHPSTAN    := $(PHP) vendor/bin/phpstan
PHPCSFIXER := $(PHP) vendor/bin/php-cs-fixer
COMPOSER   := docker run --rm -v $(PWD):/app -v $(HOME)/.composer:/tmp -w /app composer:latest composer


setup:
	$(COMPOSER) install --prefer-dist

validate:
	$(COMPOSER) validate

composer-require: OPT :=
composer-require: P :=
composer-require:
	$(COMPOSER) require $(OPT) $(P)
composer-remove: P :=
composer-remove:
	$(COMPOSER) remove $(P)

.PHONY: test
test: lint format-dry
	$(PHPUNIT) -c phpunit.xml
	$(KAHLAN) --spec=spec

.PHONY: lint
lint:
	$(PHPSTAN) analyse -c phpstan.neon

.PHONY: format format-dry
format:
	$(PHPCSFIXER) fix --rules=@PSR2 ./

format-dry:
	$(PHPCSFIXER) fix --rules=@PSR2 --dry-run --diff ./
