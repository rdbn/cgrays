.PHONY: clean assets purge_db

clean:
	lunchy stop postgres
	lunchy start postgres
	php bin/console doctrine:database:drop --force
	composer install
	php bin/console doctrine:database:create
	php bin/console doctrine:migrations:migrate -n
	php bin/console app:parse_heroes
	php bin/console app:parse_rarity
	php bin/console app:parse_quality
	php bin/console doctrine:fixtures:load --append
	php bin/console assets:install
	php bin/console cache:clear --env=prod --no-debug

assets:
	php bin/console assets:install
	php bin/console cache:clear --env=prod --no-debug

purge_db:
	lunchy stop postgres
	lunchy start postgres
	php bin/console doctrine:database:drop --force
	php bin/console doctrine:database:create
	php bin/console doctrine:migrations:migrate -n
	php bin/console app:parse_heroes
	php bin/console app:parse_rarity
	php bin/console app:parse_quality
	php bin/console doctrine:database:drop --force
	php bin/console doctrine:fixtures:load --append
	php bin/console cache:clear --env=prod --no-debug
