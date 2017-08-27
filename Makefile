.PHONY: clean assets purge_db create

create:
	php bin/console doctrine:database:create

clean:
	php bin/console doctrine:database:drop --force
	composer install
	php bin/console doctrine:database:create
	php bin/console doctrine:migrations:migrate -n
	php bin/console doctrine:fixtures:load --append
	php bin/console assets:install
	php bin/console cache:clear --env=prod --no-debug

assets:
	php bin/console assets:install
	php bin/console cache:clear --env=prod --no-debug

purge_db:
	php bin/console doctrine:database:drop --force
	php bin/console doctrine:database:create
	php bin/console doctrine:migrations:migrate -n
	php bin/console doctrine:database:drop --force
	php bin/console doctrine:fixtures:load --append
	php bin/console cache:clear --env=prod --no-debug
