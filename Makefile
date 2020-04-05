build:
	docker-compose up --build -d

setup:
	docker-compose run app composer install --optimize-autoloader
	docker-compose run node yarn install --no-lockfile
	docker-compose run node yarn encore production
	docker-compose run app php bin/console d:d:c --env=prod
	docker-compose run app php bin/console d:m:m --no-interaction --env=prod
	docker-compose exec app chown -R www-data:www-data .

end:
	docker-compose down --volumes --rmi local

fetch:
	docker-compose run app bin/console app:fetch-fx-rates --env=prod