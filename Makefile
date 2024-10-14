PORT ?= 8000
start:
	PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public
install:
	composer install
lint:
	composer exec --verbose phpcs -- --standard=PSR12 public
autoload:
	composer dump-autoload
test:
	./vendor/bin/phpunit tests/Test.php
setEnv:
	export DATABASE_URL=postgresql://sasha:12345@localhost:5432/urls