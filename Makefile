PORT ?= 8000
start:
	PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public
install:
	composer install
lint:
	composer exec --verbose phpcs -- --standard=PSR12 public src
autoload:
	composer dump-autoload
test:
	./vendor/bin/phpunit tests/Test.php
setEnv:
	export DATABASE_URL=postgresql://sasha:12345@localhost:5432/urls_689c
setEnvProd:
	export DATABASE_URL=postgresql://sasha:BFwusO460hCQU68T2eZRhCqzYUgi5nZa@dpg-csdjv8l6l47c73dcbqr0-a.oregon-postgres.render.com/urls_689c
initDB:
	psql -a -d $DATABASE_URL -f database.sql
lints:
	vendor/bin/phpstan analyse -l 9 --memory-limit 256M src 