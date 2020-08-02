PHP_VERSION := 7.2

PHP        := docker run -it --rm -v $(PWD):/app -w /app -e PHP_IDE_CONFIG='serverName=phpflags-debug' phpflags-php:$(PHP_VERSION)
PHPUNIT    := $(PHP) vendor/bin/phpunit
KAHLAN     := $(PHP) vendor/bin/kahlan
PHPSTAN    := $(PHP) vendor/bin/phpstan
PHPCSFIXER := $(PHP) vendor/bin/php-cs-fixer
COMPOSER   := docker run --rm -v $(PWD):/app -v $(HOME)/.composer:/tmp -w /app composer:latest composer

.PHONY: setup
setup: build
	$(COMPOSER) install --prefer-dist

.PHONY: build
build: build/Dockerfile-$(PHP_VERSION)
	docker build -t phpflags-php:$(PHP_VERSION) -f $< build/
build/Dockerfile-$(PHP_VERSION): build/Dockerfile.tmpl
	sed -e 's/__PHPVER__/$(PHP_VERSION)/g' $< > $@

.PHONY: validate
validate:
	$(COMPOSER) validate

.PHONY: composer-remove composer-require
composer-require: OPT :=
composer-require: P :=
composer-require:
	$(COMPOSER) require $(OPT) $(P)
composer-remove: P :=
composer-remove:
	$(COMPOSER) remove $(P)

.PHONY: test
# test: lint format-dry
test:
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
